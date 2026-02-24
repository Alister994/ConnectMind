<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Interaction extends TenantModel
{
    public const TYPE_MEETING = 'meeting';
    public const TYPE_CALL = 'call';
    public const TYPE_EMAIL = 'email';

    protected $fillable = [
        'contact_id',
        'type',
        'title',
        'notes',
        'ai_summary',
        'occurred_at',
    ];

    protected function casts(): array
    {
        return [
            'occurred_at' => 'datetime',
        ];
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }
}
