<?php

namespace App\Repositories;

use App\Models\Interaction;
use App\Repositories\Contracts\InteractionRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class InteractionRepository implements InteractionRepositoryInterface
{
    public function getByContact(int $contactId, int $perPage = 15): LengthAwarePaginator
    {
        return Interaction::where('contact_id', $contactId)
            ->orderByDesc('occurred_at')
            ->paginate($perPage);
    }

    public function find(int $id): ?Interaction
    {
        return Interaction::with('contact')->find($id);
    }

    public function create(array $data): Interaction
    {
        return Interaction::create($data);
    }

    public function update(Interaction $interaction, array $data): Interaction
    {
        $interaction->update($data);
        return $interaction->fresh();
    }

    public function delete(Interaction $interaction): bool
    {
        return $interaction->delete();
    }
}
