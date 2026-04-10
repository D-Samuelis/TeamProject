<?php

namespace App\Http\Controllers\Web\Appointment;

use App\Application\Appointment\UseCases\GetAppointment;
use App\Application\Appointment\UseCases\ListAppointments;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Appointment\GetSlotsRequest;
use App\Http\Requests\Appointment\StoreAppointmentRequest;
use App\Application\Appointment\DTO\GetSlotsDTO;
use App\Application\Appointment\DTO\CreateAppointmentDTO;
use App\Application\Appointment\UseCases\GetAvailableSlots;
use App\Application\Appointment\UseCases\CreateAppointment;

class AppointmentController extends Controller
{
    public function __construct(
        private readonly GetAvailableSlots $getSlots,
        private readonly CreateAppointment $createAppointment,
    ) {}

    public function index(ListAppointments $listAppointments)
    {
        $user = Auth::user();

        return view('web.customer.appointment.index', [
            'appointments'   => $listAppointments->execute([], $user),
        ]);
    }

    public function show(int $appointmentId, GetAppointment $getAsset)
    {
        $user     = Auth::user();
        $appointment = $getAsset->execute($appointmentId, $user);
        $appointment->load(['service', 'asset.branch.business', 'user']);

        return view('web.customer.appointment.show', [
            'appointment'    => $appointment,
        ]);
    }

    /**
     * GET /appointments/slots?asset_id=&service_id=&from=&to=
     */
    public function slots(GetSlotsRequest $request): JsonResponse
    {
        $slots = $this->getSlots->execute(
            GetSlotsDTO::fromRequest($request),
            Auth::user(),
        );

        return response()->json($slots);
    }

    /**
     * POST /appointments
     */
    public function store(StoreAppointmentRequest $request)
    {
        $appointment = $this->createAppointment->execute(
            CreateAppointmentDTO::fromRequest($request),
            Auth::id(),
            Auth::user(),
        );

        if ($request->wantsJson()) {
            return response()->json(['appointment' => $appointment], 201);
        }

        return redirect()
            ->route('myAppointments')
            ->with('success', 'Appointment booked successfully!');
    }
}
