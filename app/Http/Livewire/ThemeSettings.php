<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Preferences;

class ThemeSettings extends Component
{
    public $sidebarColor = 'primary';
    public $darkMode = false;

    public function mount()
    {
        if($preferences = auth()->user()) {
            $preferences = auth()->user()->preferences()->first();

            if ($preferences) {
                $this->sidebarColor = $preferences->sidebar_color;
                $this->darkMode = (bool)$preferences->dark_mode;
            }
        }
    }

    public function updatedSidebarColor($value)
    {
        $this->updatePreferences();
        $this->dispatch('sidebarColorChanged', ['color' => $value]);
    }

    public function updatedDarkMode($value)
    {
        $this->updatePreferences();
        session(['darkMode' => $value]); // Store dark mode state in session

        // For immediate update without page refresh
        $this->dispatch('darkModeChanged', ['isDark' => $value]);

        // Force page refresh to ensure all styles are applied correctly
        $this->dispatch('refresh-page');
    }

    private function updatePreferences()
    {
        Preferences::updateOrCreate(
            ['user_id' => auth()->id() ?? null],
            [
                'sidebar_color' => $this->sidebarColor,
                'dark_mode' => $this->darkMode,
            ]
        );
        $this->dispatch('refresh-page');

    }

    public function render()
    {
        return view('livewire.plugins');
    }
}
