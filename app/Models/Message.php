<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'body',
        'sender_id',
        'receiver_id',
        'read_at',
        'receiver_deleted_at',
        'sender_deleted_at',
    ];

    protected function casts(): array
    {
        return [
            'read_at' => 'timestamp',
            'receiver_deleted_at' => 'timestamp',
            'sender_deleted_at' => 'timestamp',
        ];
    }

    protected $appends = [
        'is_read',
    ];

    public function isRead(): Attribute
    {
        return Attribute::make(
            get: fn () => ! empty($this->read_at),
        );
    }

    // scopes
    public function scopeWhereNotDeleted(Builder $query): void
    {
        $userId = auth()->id();
        $query->where('sender_id', $userId)
            ->whereNull('sender_deleted_at')
            ->orWhere(function ($query) use ($userId) {
                $query->where('receiver_id', $userId)
                    ->whereNull('receiver_deleted_at');
            });
    }

    // Relationships
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
