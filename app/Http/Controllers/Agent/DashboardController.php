<?php

namespace App\Http\Controllers\Agent;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $agentId = $request->user()->id;

        $stats = [
            'pending_payments' => Payment::where('status', PaymentStatus::PENDING->value)->count(),
            'unassigned'       => Booking::whereNull('agent_id')
                ->whereIn('status', [BookingStatus::PENDING->value, BookingStatus::PAID->value])
                ->count(),
            'my_active'        => Booking::where('agent_id', $agentId)
                ->whereIn('status', [BookingStatus::PAID->value, BookingStatus::CONFIRMED->value])
                ->count(),
            'my_completed'     => Booking::where('agent_id', $agentId)
                ->where('status', BookingStatus::COMPLETED->value)
                ->count(),
        ];

        $pendingPayments = Payment::with(['booking:id,code,user_id,total_price', 'booking.user:id,name'])
            ->where('status', PaymentStatus::PENDING->value)
            ->oldest()
            ->limit(8)
            ->get();

        $myBookings = Booking::with(['user:id,name', 'tourPackage:id,title,start_date'])
            ->where('agent_id', $agentId)
            ->whereIn('status', [BookingStatus::PAID->value, BookingStatus::CONFIRMED->value])
            ->latest()
            ->limit(8)
            ->get();

        return view('agent.dashboard', compact('stats', 'pendingPayments', 'myBookings'));
    }
}