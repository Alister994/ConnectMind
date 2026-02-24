<?php

namespace App\Services;

use App\Models\Contact;
use App\Models\Interaction;
use App\Repositories\Contracts\InteractionRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class InteractionService
{
    public function __construct(
        protected InteractionRepositoryInterface $interactionRepository
    ) {}

    public function getByContact(int $contactId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->interactionRepository->getByContact($contactId, $perPage);
    }

    public function find(int $id): ?Interaction
    {
        return $this->interactionRepository->find($id);
    }

    public function create(array $data): Interaction
    {
        $interaction = $this->interactionRepository->create($data);
        $this->updateContactLastInteraction($interaction->contact_id);
        return $interaction;
    }

    public function update(Interaction $interaction, array $data): Interaction
    {
        return $this->interactionRepository->update($interaction, $data);
    }

    public function delete(Interaction $interaction): bool
    {
        $contactId = $interaction->contact_id;
        $deleted = $this->interactionRepository->delete($interaction);
        if ($deleted) {
            $this->updateContactLastInteraction($contactId);
        }
        return $deleted;
    }

    protected function updateContactLastInteraction(int $contactId): void
    {
        $last = Interaction::where('contact_id', $contactId)->orderByDesc('occurred_at')->first();
        Contact::where('id', $contactId)->update([
            'last_interaction_at' => $last?->occurred_at,
        ]);
    }
}
