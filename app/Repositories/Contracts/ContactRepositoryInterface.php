<?php

namespace App\Repositories\Contracts;

use App\Models\Contact;

interface ContactRepositoryInterface
{
    public function all(array $filters = []);

    public function find(int $id): ?Contact;

    public function create(array $data): Contact;

    public function update(Contact $contact, array $data): Contact;

    public function delete(Contact $contact): bool;
}
