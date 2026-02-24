<?php

namespace App\Repositories\Contracts;

use App\Models\Interaction;

interface InteractionRepositoryInterface
{
    public function getByContact(int $contactId, int $perPage = 15);

    public function find(int $id): ?Interaction;

    public function create(array $data): Interaction;

    public function update(Interaction $interaction, array $data): Interaction;

    public function delete(Interaction $interaction): bool;
}
