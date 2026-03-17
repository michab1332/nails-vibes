<?php

namespace App\Http\Controllers;

use App\Services\PriceItemService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PriceItemController extends Controller
{
    private $page = "PriceItems";

    public function __construct(protected PriceItemService $priceItemService) {}

    public function index(): Response
    {
        return Inertia::render("$this->page/Index", [
            'data' => $this->priceItemService->index(),
        ]);
    }
}
