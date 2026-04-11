<?php

namespace App\Http\Controllers\Web\Appointment;

use App\Application\Appointment\DTO\RescheduleAppointmentDTO;
use App\Application\Appointment\DTO\UpdateAppointmentDTO;
use App\Application\Appointment\UseCases\GetAppointment;
use App\Application\Appointment\UseCases\ListAppointments;
use App\Application\Appointment\UseCases\DeleteAppointment;
use App\Application\Appointment\UseCases\RescheduleAppointment;
use App\Application\Appointment\UseCases\UpdateAppointment;
use App\Domain\Appointment\Interfaces\AppointmentRepositoryInterface;
use App\Http\Requests\Appointment\RescheduleAppointmentRequest;
use App\Http\Requests\Appointment\UpdateAppointmentRequest;
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
     * Always public so no user is needed for auth
     */
    public function slots(GetSlotsRequest $request): JsonResponse
    {
        $slots = $this->getSlots->execute(
            GetSlotsDTO::fromRequest($request),
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
        );

        if ($request->wantsJson()) {
            return response()->json(['appointment' => $appointment], 201);
        }

        return redirect()
            ->route('myAppointments')
            ->with('success', 'Appointment booked successfully!');
    }

    public function update(int $appointmentId, UpdateAppointmentRequest $request, UpdateAppointment $useCase)
    {
        $useCase->execute(
            UpdateAppointmentDTO::fromRequest($appointmentId, $request),
            Auth::user(),
        );

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Appointment updated successfully.');
    }

    public function reschedule(int $appointmentId, RescheduleAppointmentRequest $request, RescheduleAppointment $useCase)
    {
        $appointment = $useCase->execute(
            new RescheduleAppointmentDTO(
                appointmentId: $appointmentId,
                date:          $request->validated('date'),
                startAt:       $request->validated('start_at'),
            ),
            Auth::user(),
        );

        if ($request->wantsJson()) {
            return response()->json(['appointment' => $appointment]);
        }

        return back()->with('success', 'Appointment rescheduled successfully.');
    }

    public function delete(int $appointmentId, AppointmentRepositoryInterface $appointmentRepo, DeleteAppointment $useCase)
    {
        $appointment = $appointmentRepo->findById($appointmentId);
        abort_if(!$appointment, 404);

        $useCase->execute($appointmentId, Auth::id());

        return redirect()
            ->route('myAppointments')
            ->with('success', 'Appointment deleted successfully.');
    }
}
