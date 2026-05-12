<?php

namespace App\Http\Livewire\Reports;

use App\Models\DutyScheduling;
use App\Models\User;
use App\Models\Patient;
use App\Models\Bills;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportView extends Component
{
    public $start_date;
    public $end_date;
    public $doctor_id = null; // Filter for doctor
    public $patient_id = null; // Patient filter
    public $reportData = [];
    public $nb_of_appointments = 0;
    public $nb_of_patients = 0;
    public $doctorShareTotal = 0; // Total share for doctors
    public $centerShareTotal = 0; // Total share for centers
    public $totalBilledAmount = 0; // Total billed amount
    public $totalPaidAmount = 0;  // Total paid amount
    public $totalRemainingAmount = 0; // Total remaining amount
    public $canApplyFilter = true; // Button visibility control
    public $patients = []; // List of patients
    public $doctors = []; // List of doctors

    public $allDoctors;

    public function mount()
    {
        $this->start_date = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->end_date = Carbon::now()->endOfMonth()->format('Y-m-d');
        $this->loadDoctors(); // Load doctors
        $this->loadPatients(); // Load patients related to arrived appointments
        $this->generateReport(); // Generate report with default data
    }

    public function applyFilters()
    {
        if (!$this->validateDateRange()) {
            return;
        }

        // Reset totals before generating the new report
        $this->doctorShareTotal = 0;
        $this->centerShareTotal = 0;
        $this->totalBilledAmount = 0;
        $this->totalPaidAmount = 0;
        $this->totalRemainingAmount = 0;

        // Generate the report after the filter is applied
        $this->generateReport();
    }


    public function filterByPatient($patientId)
    {
        $this->patient_id = $patientId;
        $this->generateReport(); // Regenerate report when patient filter is applied
    }

    public function resetFilter()
    {
        $this->doctor_id = null;
        $this->patient_id = null;
        $this->generateReport(); // Regenerate report when filters are reset
    }

    // Method to load doctors
    protected function loadDoctors()
    {
        $this->doctors = User::where('type', 'Doctor')->orderBy('fname')->get(['id', 'fname', 'lname']);
        $this->allDoctors = User::where('type', 'Doctor')
            ->select('id', 'fname', 'lname', 'email', 'created_at')
            ->orderBy('fname')
            ->get();
    }

    // Method to load patients who have arrived
    protected function loadPatients()
    {
        // Only load patients that are associated with arrived appointments
        $this->patients = DutyScheduling::where('patient_arrived', true)
            ->with('patient') // Ensure patient details are loaded with the appointment
            ->get()
            ->pluck('patient') // Get the related patient model
            ->unique('id'); // Make sure the list contains unique patients
    }

    public function updated($propertyName)
    {
        if ($propertyName == 'start_date' || $propertyName == 'end_date' || $propertyName == 'doctor_id') {
            $this->canApplyFilter = true;
        }
    }

    public function generateReport()
    {
        // First, we get the appointments based on the filter criteria
        $appointmentQuery = DutyScheduling::with('doctor', 'patient')
            ->where('patient_arrived', true)
            ->whereDate('start_date', '>=', $this->start_date)
            ->whereDate('end_date', '<=', $this->end_date);

        // Filter by doctor if selected
        if ($this->doctor_id) {
            $appointmentQuery->where('doctor_id', $this->doctor_id);
        }

        // Filter by patient if selected
        if ($this->patient_id) {
            $appointmentQuery->where('patient_id', $this->patient_id);
        }

        $appointments = $appointmentQuery->get();

        $this->nb_of_appointments = $appointments->count();
        $this->nb_of_patients = $appointments->pluck('patient_id')->unique()->count();

        // Reset totals to avoid double counting
        $this->doctorShareTotal = 0;
        $this->centerShareTotal = 0;
        $this->totalBilledAmount = 0;
        $this->totalPaidAmount = 0;
        $this->totalRemainingAmount = 0;

        // Group by doctor
        $doctorGroups = $appointments->groupBy('doctor_id');

        // Now, we'll process the bills for each doctor
        $this->reportData = collect();

        foreach ($doctorGroups as $doctorId => $doctorAppointments) {
            $doctor = User::find($doctorId);
            $doctorName = $doctor ? "Dr. {$doctor->fname} {$doctor->lname}" : 'Unknown';
            $details = DB::table('user_details')->where('user_id', $doctorId)->first();

            $doctorPercentage = $details->dr_percentage ?? 0;
            $centerPercentage = $details->center_percentage ?? 0;

            // Get all appointment IDs for this doctor
            $appointmentIds = $doctorAppointments->pluck('id')->toArray();

            // Get all bills for these appointments
            $bills = Bills::whereIn('appointment_id', $appointmentIds)
                ->get();

            // Calculate totals for this doctor
            $totalBilled = $bills->sum('total');
            $totalPaid = $bills->sum('paid');
            $totalRemaining = $totalBilled - $totalPaid;

            // Calculate doctor and center shares based on percentages
            $doctorShare = $totalPaid * ($doctorPercentage / 100);
            $centerShare = $totalPaid * ($centerPercentage / 100);

            // Add to overall totals
            $this->totalBilledAmount += $totalBilled;
            $this->totalPaidAmount += $totalPaid;
            $this->totalRemainingAmount += $totalRemaining;
            $this->doctorShareTotal += $doctorShare;
            $this->centerShareTotal += $centerShare;

            // Add to report data
            $this->reportData->push([
                'doctor_name' => $doctorName,
                'total' => $totalBilled,
                'paid' => $totalPaid,
                'remaining' => $totalRemaining,
                'doctor_price' => $doctorShare,
                'center' => $centerShare,
                'doctor_percentage' => $doctorPercentage,
                'center_percentage' => $centerPercentage,
                'appointment_count' => $doctorAppointments->count(),
                'patient_count' => $doctorAppointments->pluck('patient_id')->unique()->count()
            ]);
        }

        // Convert to array values
        $this->reportData = $this->reportData->values()->toArray();
    }
    public function validateDateRange()
    {
        if ($this->start_date > $this->end_date) {
            $this->addError('start_date', 'Start date cannot be after end date.');
            $this->addError('end_date', 'End date cannot be before start date.');
            return false;
        }

        // Clear any previous errors if validation passes
        $this->resetErrorBag(['start_date', 'end_date']);
        return true;
    }


    public function exportPDF()
    {
        $data = [
            'reportData' => $this->reportData,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'nb_of_appointments' => $this->nb_of_appointments,
            'nb_of_patients' => $this->nb_of_patients,
            'doctorShareTotal' => $this->doctorShareTotal,
            'centerShareTotal' => $this->centerShareTotal,
            'totalBilledAmount' => $this->totalBilledAmount,
            'totalPaidAmount' => $this->totalPaidAmount,
            'totalRemainingAmount' => $this->totalRemainingAmount
        ];

        $pdf = Pdf::loadView('exports.report-pdf', $data)->setPaper('a4', 'portrait');
        return response()->streamDownload(fn () => print($pdf->output()), 'doctor_report.pdf');
    }

    public function render()
    {
        return view('livewire.Reports.report-view');
    }
}
