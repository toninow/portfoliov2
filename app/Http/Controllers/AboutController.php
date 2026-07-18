<?php

namespace App\Http\Controllers;

use App\Models\Certification;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Profile;
use App\Models\SkillGroup;
use App\Models\Technology;
use Illuminate\View\View;

class AboutController extends Controller
{
    public function __invoke(): View
    {
        return view('pages.about', [
            'profile' => Profile::current(),
            'experiences' => Experience::orderBy('sort')->get(),
            'education' => Education::orderBy('sort')->get(),
            'certifications' => Certification::orderBy('sort')->get(),
            'skillGroups' => SkillGroup::with('skills')->orderBy('sort')->get(),
            'technologies' => Technology::orderBy('sort')->get()->groupBy('area'),
        ]);
    }
}
