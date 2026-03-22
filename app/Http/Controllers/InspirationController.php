<?php

namespace App\Http\Controllers;

use App\Services\InspirationService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class InspirationController extends Controller
{
    public function __construct(protected InspirationService $inspirationService) {}

    public function index(Request $request): Response
    {
        $inspirations = $this->inspirationService->getAllInspirations();

        return Inertia::render('Inspirations/Index', [
            'inspirations' => $inspirations,
        ]);
    }
}
