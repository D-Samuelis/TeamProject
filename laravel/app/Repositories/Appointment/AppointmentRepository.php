<?php

namespace App\Repositories\Appointment;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use App\Domain\Appointment\Interfaces\AppointmentRepositoryInterface;
use App\Models\Auth\User;
use App\Models\Business\Appointment;

class AppointmentRepository implements AppointmentRepositoryInterface
{
    // ── SCHEDULING ───────────────────────────────────────────────

    /**
     * Returns taken time slots (H:i) for a given asset on a given date.
     * Used by the availability engine to exclude already-booked times.
     */
    public function getTakenSlots(int $assetId, Carbon $date): Collection
    {
        return Appointment::query()
            ->where('asset_id', $assetId)
            ->whereDate('date', $date->toDateString())
            ->whereNotIn('status', ['cancelled'])
            ->pluck('start_at')
            ->map(fn($t) => Carbon::parse($t)->format('H:i'));
    }

    // ── DATA PERSISTENCE ─────────────────────────────────────────

    public function save(array $data): Appointment
    {
        return Appointment::create($data);
    }

    public function findById(int $id): ?Appointment
    {
        return Appointment::find($id);
    }

    public function updateStatus(Appointment $appointment, string $status): Appointment
    {
        $appointment->update(['status' => $status]);
        return $appointment->fresh();
    }

    // ── QUERIES ──────────────────────────────────────────────────

    /**
     * All appointments belonging to a specific customer.
     * Ordered most-recent first so the "my bookings" list is naturally sorted.
     */
    public function listForUser(User $user): Collection
    {
        return Appointment::query()
            ->where('user_id', $user->id)
            ->with(['branchService.service', 'branch.business', 'asset'])
            ->latest('date')
            ->get();
    }

    /**
     * All appointments for an asset, optionally constrained to a date range.
     * Used by staff/managers to see an asset's schedule.
     */
    public function listForAsset(int $assetId, ?Carbon $from = null, ?Carbon $to = null): Collection
    {
        $query = Appointment::query()
            ->where('asset_id', $assetId)
            ->with(['user', 'branchService.service']);

        if ($from) {
            $query->whereDate('date', '>=', $from->toDateString());
        }

        if ($to) {
            $query->whereDate('date', '<=', $to->toDateString());
        }

        return $query->orderBy('date')->orderBy('start_at')->get();
    }

    /**
     * All appointments for a branch, optionally constrained to a date range.
     * Used by branch managers to see the branch's full schedule.
     */
    public function listForBranch(int $branchId, ?Carbon $from = null, ?Carbon $to = null): Collection
    {
        $query = Appointment::query()
            ->where('branch_id', $branchId)
            ->with(['user', 'branchService.service', 'asset']);

        if ($from) {
            $query->whereDate('date', '>=', $from->toDateString());
        }

        if ($to) {
            $query->whereDate('date', '<=', $to->toDateString());
        }

        return $query->orderBy('date')->orderBy('start_at')->get();
    }
}
