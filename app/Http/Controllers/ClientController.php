<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientRequest;
use App\Models\Client;
use App\Services\ClientService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ClientController extends Controller
{
    private $page = "Clients";

    public function __construct(protected ClientService $clientService) {}

    public function index(): Response
    {
        return Inertia::render("$this->page/Index", [
            'data' => $this->clientService->index(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render("$this->page/Create");
    }

    public function store(ClientRequest $request): RedirectResponse
    {
        $this->clientService->store($request->validated());

        return redirect()->route('admin.clients.index');
    }

    public function edit(Client $client): Response
    {
        return Inertia::render("$this->page/Edit", [
            'client' => $client,
        ]);
    }

    public function update(ClientRequest $request, Client $client): RedirectResponse
    {
        $this->clientService->update($client, $request->validated());

        return redirect()->route('admin.clients.index');
    }

    public function destroy(Client $client): RedirectResponse
    {
        $this->clientService->destroy($client);

        return redirect()->route('admin.clients.index');
    }
}
