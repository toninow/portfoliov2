<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('experiences')
            ->where('company', 'R&B Studio · Marketing digital')
            ->update([
                'company' => 'R&B Studio',
                'company_sector' => 'Marketing digital',
            ]);
    }

    public function down(): void
    {
        DB::table('experiences')
            ->where('company', 'R&B Studio')
            ->where('company_sector', 'Marketing digital')
            ->update([
                'company' => 'R&B Studio · Marketing digital',
            ]);
    }
};
