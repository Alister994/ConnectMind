<?php

namespace App\Services;

use App\Models\Contact;
use App\Repositories\Contracts\ContactRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class ContactService
{
    public function __construct(
        protected ContactRepositoryInterface $contactRepository
    ) {}

    public function list(array $filters = []): LengthAwarePaginator
    {
        return $this->contactRepository->all($filters);
    }

    public function get(int $id): ?Contact
    {
        return $this->contactRepository->find($id);
    }

    public function create(array $data): Contact
    {
        return $this->contactRepository->create($data);
    }

    public function update(Contact $contact, array $data): Contact
    {
        return $this->contactRepository->update($contact, $data);
    }

    public function delete(Contact $contact): bool
    {
        return $this->contactRepository->delete($contact);
    }
}
