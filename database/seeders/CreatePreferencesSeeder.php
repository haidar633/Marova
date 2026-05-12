<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Preferences;

class CreatePreferencesSeeder extends Seeder
{
    public function run()
    {
        Preferences::create([
            'user_id' => 1,
            'sidebar_color' => 'primary',
            'dark_mode' => 0,
        ]);
    }
}
