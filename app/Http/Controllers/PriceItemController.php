<?php

namespace App\Http\Controllers;

use App\Http\Requests\PriceItemRequest;
use App\Models\PriceItem;
use App\Services\PriceItemService;
use Illuminate\Http\RedirectResponse;
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

    public function create(): Response
    {
        return Inertia::render("$this->page/Create", $this->priceItemService->create());
    }

    public function store(PriceItemRequest $request): RedirectResponse
    {
        $this->priceItemService->store($request->validated());

        return redirect()->route('admin.price-items.index');
    }

    public function edit(PriceItem $priceItem): Response
    {
        return Inertia::render("$this->page/Edit", $this->priceItemService->edit($priceItem));
    }

    public function update(PriceItemRequest $request, PriceItem $priceItem): RedirectResponse
    {
        $this->priceItemService->update($priceItem, $request->validated());

        return redirect()->route('admin.price-items.index');
    }

    public function destroy(PriceItem $priceItem): RedirectResponse
    {
        $this->priceItemService->destroy($priceItem);

        return redirect()->route('admin.price-items.index');
    }
}
