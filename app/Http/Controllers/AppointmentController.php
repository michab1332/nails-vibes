<?php

namespace App\Http\Controllers;

use App\Http\Requests\AppointmentRequest;
use App\Models\Appointment;
use App\Services\AppointmentService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class AppointmentController extends Controller
{
    private $page = "Appointments";

    public function __construct(protected AppointmentService $appointmentService) {}

    public function index(): Response
    {
        return Inertia::render("$this->page/Index", $this->appointmentService->index(request()->all()));
    }

    public function calendar(): Response
    {
        return Inertia::render("AppointmentsCalendar/Index", $this->appointmentService->index(request()->all()));
    }

    public function create(): Response
    {
        return Inertia::render("$this->page/Create", $this->appointmentService->create());
    }

    public function store(AppointmentRequest $request): RedirectResponse
    {
        $this->appointmentService->store($request->validated());

        return redirect()->route('admin.appointments.index');
    }

    public function edit(Appointment $appointment): Response
    {
        return Inertia::render("$this->page/Edit", $this->appointmentService->edit($appointment));
    }

    public function update(AppointmentRequest $request, Appointment $appointment): RedirectResponse
    {
        $this->appointmentService->update($appointment, $request->validated());

        return redirect()->route('admin.appointments.index');
    }

    public function destroy(Appointment $appointment): RedirectResponse
    {
        $this->appointmentService->destroy($appointment);

        return redirect()->route('admin.appointments.index');
    }
}
