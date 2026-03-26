<?php

namespace App\Services;

use App\Models\Appointment;
use Carbon\Carbon;

class DashboardService extends BaseService
{
    public function __construct(protected Appointment $appointment)
    {
    }

    public function getDashboardData(): array
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        $monthlyStats = [
            'total_appointments' => $this->appointment
                ->whereBetween('start_time', [$startOfMonth, $endOfMonth])
                ->count(),
            'monthly_revenue' => $this->appointment
                ->whereBetween('start_time', [$startOfMonth, $endOfMonth])
                ->where('status', \App\Enums\AppointmentStatus::COMPLETED)
                ->sum('total_price'),
        ];

        return [
            'upcomingAppointments' => $this->getUpcomingAppointments(),
            'monthlyStats' => $monthlyStats,
        ];
    }

    protected function getUpcomingAppointments()
    {
        $now = Carbon::now();
        
        // 1. Get today's appointments that are starting from now onwards
        $todayUpcoming = $this->appointment->with(['client', 'priceItems'])
            ->where('start_time', '>=', $now)
            ->whereDate('start_time', $now->toDateString())
            ->orderBy('start_time', 'asc')
            ->get();

        if ($todayUpcoming->isNotEmpty()) {
            return [
                'type' => 'today',
                'items' => $todayUpcoming
            ];
        }

        // 2. If no more today, get tomorrow's appointments
        $tomorrow = Carbon::tomorrow();
        $tomorrowAppointments = $this->appointment->with(['client', 'priceItems'])
            ->whereDate('start_time', $tomorrow->toDateString())
            ->orderBy('start_time', 'asc')
            ->get();

        if ($tomorrowAppointments->isNotEmpty()) {
            return [
                'type' => 'tomorrow',
                'items' => $tomorrowAppointments
            ];
        }

        // 3. If nothing today and tomorrow, find the absolute NEXT appointment in the future
        $firstNext = $this->appointment
            ->where('start_time', '>', $tomorrow->endOfDay())
            ->orderBy('start_time', 'asc')
            ->first();

        if ($firstNext) {
            $nextDate = $firstNext->start_time->toDateString();
            $allOnNextDay = $this->appointment->with(['client', 'priceItems'])
                ->whereDate('start_time', $nextDate)
                ->orderBy('start_time', 'asc')
                ->get();

            return [
                'type' => 'next_available',
                'items' => $allOnNextDay,
                'next_date' => $nextDate
            ];
        }

        return [
            'type' => 'none',
            'items' => []
        ];
    }
}
