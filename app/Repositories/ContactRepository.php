<?php

namespace App\Repositories;

use App\Models\Contact;
use App\Repositories\Contracts\ContactRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class ContactRepository implements ContactRepositoryInterface
{
    public function all(array $filters = []): LengthAwarePaginator
    {
        $query = Contact::query()->with(['tags']);

        if (!empty($filters['tag_id'])) {
            $query->whereHas('tags', fn ($q) => $q->where('tags.id', $filters['tag_id']));
        }
        if (!empty($filters['search'])) {
            $s = $filters['search'];
            $query->where(fn ($q) => $q->where('name', 'like', "%{$s}%")
                ->orWhere('email', 'like', "%{$s}%"));
        }

        return $query->orderBy('name')->paginate($filters['per_page'] ?? 15);
    }

    public function find(int $id): ?Contact
    {
        return Contact::with(['tags', 'interactions'])->find($id);
    }

    public function create(array $data): Contact
    {
        $tags = $data['tags'] ?? [];
        unset($data['tags']);
        $contact = Contact::create($data);
        if (!empty($tags)) {
            $contact->tags()->sync($tags);
        }
        return $contact->load('tags');
    }

    public function update(Contact $contact, array $data): Contact
    {
        $tags = $data['tags'] ?? null;
        unset($data['tags']);
        $contact->update($data);
        if (is_array($tags)) {
            $contact->tags()->sync($tags);
        }
        return $contact->fresh(['tags']);
    }

    public function delete(Contact $contact): bool
    {
        return $contact->delete();
    }
}
