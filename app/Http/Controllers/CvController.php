<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CvController extends Controller
{
    public function __invoke(): Response
    {
        $path = Profile::current()->cv_path;

        if (! $path || ! Storage::disk('public')->exists($path)) {
            throw new NotFoundHttpException('CV no disponible.');
        }

        return response()->file(Storage::disk('public')->path($path));
    }
}
