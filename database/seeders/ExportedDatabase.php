<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExportedDatabase extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::unprepared(file_get_contents(base_path() .'\database\seeders\schooler.sql'));
    }
}
