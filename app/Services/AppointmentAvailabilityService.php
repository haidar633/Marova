<?php

namespace App\Services;

use App\Models\DoctorAvailability;
use App\Models\NonWorkingDays;
use App\Models\User;
use Carbon\Carbon;

class AppointmentAvailabilityService
{
    /**
     * Check if a doctor is available at a specific date and time
     *
     * @param int $doctorId The doctor's user ID
     * @param string $date The date in Y-m-d format
     * @param string $startTime The appointment start time in H:i format
     * @param string $endTime The appointment end time in H:i format (optional)
     * @return array ['available' => bool, 'reason' => string|null]
     */
    public function isDoctorAvailable(int $doctorId, string $date, string $startTime, string $endTime = null): array
    {
        // Parse the date and time inputs
        $appointmentDate = Carbon::parse($date);
        $dayOfWeek = $appointmentDate->format('l'); // Get day name (Monday, Tuesday, etc.)
        $appointmentStartTime = Carbon::parse($date . ' ' . $startTime);
        $appointmentEndTime = $endTime ? Carbon::parse($date . ' ' . $endTime) : $appointmentStartTime->copy()->addHour();

        // 1. First check for non-working days (they take precedence)
        $nonWorkingDay = $this->checkNonWorkingDay($doctorId, $appointmentDate, $appointmentStartTime, $appointmentEndTime);
        if (!$nonWorkingDay['available']) {
            return $nonWorkingDay;
        }

        // 2. Then check regular doctor availability
        return $this->checkDoctorRegularAvailability($doctorId, $dayOfWeek, $appointmentStartTime, $appointmentEndTime);
    }

    /**
     * Check if a date falls within any non-working days for a doctor
     */
    private function checkNonWorkingDay(int $doctorId, Carbon $date, Carbon $startTime, Carbon $endTime): array
    {
        $dateStr = $date->format('Y-m-d');
        $nonWorkingDays = NonWorkingDays::where('user_id', $doctorId)
            ->where('is_active', true)
            ->get();

        foreach ($nonWorkingDays as $nonWorkingDay) {
            if ($nonWorkingDay->isNonWorkingDay($dateStr)) {
                // Check if specific times are set
                if ($nonWorkingDay->start_time && $nonWorkingDay->end_time) {
                    $blockStartTime = Carbon::parse($dateStr . ' ' . $nonWorkingDay->start_time->format('H:i:s'));
                    $blockEndTime = Carbon::parse($dateStr . ' ' . $nonWorkingDay->end_time->format('H:i:s'));

                    // Check if appointment time overlaps with blocked time
                    if ($startTime->between($blockStartTime, $blockEndTime) ||
                        $endTime->between($blockStartTime, $blockEndTime) ||
                        ($startTime <= $blockStartTime && $endTime >= $blockEndTime)) {
                        return [
                            'available' => false,
                            'reason' => 'The doctor is unavailable during this specific time block'
                        ];
                    }
                } else {
                    // Whole day is blocked
                    return [
                        'available' => false,
                        'reason' => 'The doctor is not available on this date'
                    ];
                }
            }
        }

        return ['available' => true, 'reason' => null];
    }

    /**
     * Check if a time slot falls within a doctor's regular availability for a day of week
     */
    private function checkDoctorRegularAvailability(int $doctorId, string $dayOfWeek, Carbon $startTime, Carbon $endTime): array
    {
        $doctorAvailability = DoctorAvailability::where('user_id', $doctorId)
            ->where('day', $dayOfWeek)
            ->get();

        if ($doctorAvailability->isEmpty()) {
            return [
                'available' => false,
                'reason' => 'The doctor does not have regular hours on this day'
            ];
        }

        // Check if the appointment time falls within any availability block
        foreach ($doctorAvailability as $availability) {
            $availStartTime = Carbon::parse($startTime->format('Y-m-d') . ' ' . $availability->start_time->format('H:i:s'));
            $availEndTime = Carbon::parse($startTime->format('Y-m-d') . ' ' . $availability->end_time->format('H:i:s'));

            if ($startTime->between($availStartTime, $availEndTime) && $endTime->between($availStartTime, $availEndTime)) {
                return ['available' => true, 'reason' => null];
            }
        }

        return [
            'available' => false,
            'reason' => 'The requested time is outside the doctor\'s regular hours'
        ];
    }

    /**
     * Get available time slots for a doctor on a specific date
     *
     * @param int $doctorId
     * @param string $date Y-m-d format
     * @param int $intervalMinutes Default 30-minute slots
     * @return array Array of available time slots ['start' => 'H:i', 'end' => 'H:i']
     */
    public function getAvailableTimeSlots(int $doctorId, string $date, int $intervalMinutes = 30): array
    {
        $appointmentDate = Carbon::parse($date);
        $dayOfWeek = $appointmentDate->format('l');
        $availableSlots = [];

        // Get doctor's regular availability for this day
        $regularAvailability = DoctorAvailability::where('user_id', $doctorId)
            ->where('day', $dayOfWeek)
            ->get();

        if ($regularAvailability->isEmpty()) {
            return $availableSlots; // Doctor doesn't work on this day
        }

        // Get all non-working days that might affect this date
        $nonWorkingDays = NonWorkingDays::where('user_id', $doctorId)
            ->where('is_active', true)
            ->get()
            ->filter(function ($item) use ($date) {
                return $item->isNonWorkingDay($date);
            });

        // Generate time slots based on regular availability
        foreach ($regularAvailability as $availability) {
            $startTime = Carbon::parse($date . ' ' . $availability->start_time->format('H:i:s'));
            $endTime = Carbon::parse($date . ' ' . $availability->end_time->format('H:i:s'));

            $currentSlot = $startTime->copy();

            while ($currentSlot->addMinutes($intervalMinutes) <= $endTime) {
                $slotStart = $currentSlot->copy()->subMinutes($intervalMinutes);
                $slotEnd = $currentSlot->copy();

                // Check if this slot conflicts with any non-working day
                $isBlocked = false;
                foreach ($nonWorkingDays as $nonWorkingDay) {
                    if ($nonWorkingDay->start_time && $nonWorkingDay->end_time) {
                        $blockStart = Carbon::parse($date . ' ' . $nonWorkingDay->start_time->format('H:i:s'));
                        $blockEnd = Carbon::parse($date . ' ' . $nonWorkingDay->end_time->format('H:i:s'));

                        if ($slotStart->between($blockStart, $blockEnd) ||
                            $slotEnd->between($blockStart, $blockEnd) ||
                            ($slotStart <= $blockStart && $slotEnd >= $blockEnd)) {
                            $isBlocked = true;
                            break;
                        }
                    } else {
                        // Whole day is blocked
                        $isBlocked = true;
                        break;
                    }
                }

                if (!$isBlocked) {
                    $availableSlots[] = [
                        'start' => $slotStart->format('H:i'),
                        'end' => $slotEnd->format('H:i')
                    ];
                }
            }
        }

        return $availableSlots;
    }
}
