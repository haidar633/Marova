<?php

namespace App\Http\Livewire;

use App\Models\Attempt;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use DB;

class Dashboard extends Component
{
    public function render()
    {
        $currentYear = date('Y');
        $monthlyAttempts = Attempt::whereYear('attempt_date', $currentYear)
            ->selectRaw('MONTH(attempt_date) as month, COUNT(*) as count')
            ->groupBy('month')
            ->pluck('count', 'month')
            ->toArray();

        $monthlyLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $monthlyCounts = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthlyCounts[] = $monthlyAttempts[$m] ?? 0;
        }

        // 2. Yearly Total Attempts (Bar Chart)
        $yearlyAttempts = Attempt::selectRaw('YEAR(attempt_date) as year, COUNT(*) as count')
            ->groupBy('year')
            ->orderBy('year')
            ->get();
        
        $yearlyLabels = $yearlyAttempts->pluck('year')->toArray();
        $yearlyCounts = $yearlyAttempts->pluck('count')->toArray();

        // 3. Category Distribution (Doughnut Chart)
        $categoryDistribution = Attempt::selectRaw('attempt_type, COUNT(*) as count')
            ->groupBy('attempt_type')
            ->get();
        
        $categoryLabels = $categoryDistribution->pluck('attempt_type')->map(fn($t) => ucfirst($t))->toArray();
        $categoryCounts = $categoryDistribution->pluck('count')->toArray();

        // Stats
        $totalAttemptsYTD = Attempt::whereYear('attempt_date', $currentYear)->count();
        $allTimeAttempts = Attempt::count();
        $successRate = $allTimeAttempts > 0 
            ? round((Attempt::where('successful', true)->count() / $allTimeAttempts) * 100, 1) 
            : 0;

        return view('livewire.dashboard', [
            'monthlyLabels' => $monthlyLabels,
            'monthlyCounts' => $monthlyCounts,
            'yearlyLabels' => $yearlyLabels,
            'yearlyCounts' => $yearlyCounts,
            'categoryLabels' => $categoryLabels,
            'categoryCounts' => $categoryCounts,
            'totalAttemptsYTD' => $totalAttemptsYTD,
            'allTimeAttempts' => $allTimeAttempts,
            'successRate' => $successRate,
            'currentYear' => $currentYear
        ]);
    }
}
