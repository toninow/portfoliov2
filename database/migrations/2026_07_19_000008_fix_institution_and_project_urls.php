<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('education', function (Blueprint $table) {
            if (! Schema::hasColumn('education', 'institution_url')) {
                $table->string('institution_url')->nullable()->after('institution');
            }
        });

        DB::table('experiences')
            ->where(function ($query) {
                $query->where('company', 'like', '%Cruz Roja%')
                    ->orWhere('company', 'like', '%ISTCRE%');
            })
            ->update(['company_url' => 'https://istcre.edu.ec/']);

        DB::table('experiences')
            ->where('company', 'like', '%Algoritmun%')
            ->update(['company_url' => 'https://algoritmun.com/']);

        DB::table('education')
            ->where('institution', 'like', '%Yavirac%')
            ->update(['institution_url' => 'https://yavirac.edu.ec/']);

        $projectUrlFixes = [
            // Official ISTCRE site replaced the old cruzrojainstituto.edu.ec domain (now 503).
            'https://www.cruzrojainstituto.edu.ec/' => 'https://istcre.edu.ec/',
            'https://www.cruzrojainstituto.edu.ec' => 'https://istcre.edu.ec/',
            'https://cruzrojainstituto.edu.ec/' => 'https://istcre.edu.ec/',
            'https://cruzrojainstituto.edu.ec' => 'https://istcre.edu.ec/',
            // Continuing education moved to cec.istcre.edu.ec
            'https://www.cruzrojainstituto.edu.ec/cec' => 'https://cec.istcre.edu.ec/',
            'https://cruzrojainstituto.edu.ec/cec' => 'https://cec.istcre.edu.ec/',
            // www.algoritmun.com returns 404; apex works
            'https://www.algoritmun.com/' => 'https://algoritmun.com/',
            'https://www.algoritmun.com' => 'https://algoritmun.com/',
        ];

        foreach ($projectUrlFixes as $from => $to) {
            DB::table('projects')->where('url', $from)->update(['url' => $to]);
        }

        // Dead or hijacked domains: keep the project, remove the broken external link.
        $deadUrls = [
            'https://productoscalma.net/',
            'https://productoscalma.net',
            'https://internetnetlife.net.ec/',
            'https://internetnetlife.net.ec',
            'https://ventas-celerity.com/',
            'https://ventas-celerity.com',
            // Domain now redirects to an unrelated site (pinup.com.ec).
            'https://institutodelaciudad.com.ec/',
            'https://institutodelaciudad.com.ec',
            'http://institutodelaciudad.com.ec/',
            'http://institutodelaciudad.com.ec',
        ];

        DB::table('projects')->whereIn('url', $deadUrls)->update(['url' => null]);
    }

    public function down(): void
    {
        Schema::table('education', function (Blueprint $table) {
            if (Schema::hasColumn('education', 'institution_url')) {
                $table->dropColumn('institution_url');
            }
        });
    }
};
