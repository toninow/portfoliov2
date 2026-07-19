<?php

use App\Models\Education;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Education::query()
            ->where('institution', 'like', '%Yavirac%')
            ->update([
                'start_year' => '2017',
                'end_year' => '2021',
            ]);
    }

    public function down(): void
    {
        Education::query()
            ->where('institution', 'like', '%Yavirac%')
            ->update([
                'start_year' => '2017',
                'end_year' => '2019',
            ]);
    }
};
