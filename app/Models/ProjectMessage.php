<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'project_id',
        'sender_id',
        'message',
        'attachment_path',
        'original_filename',
        'mime_type',
        'file_size',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(ProjectConversation::class, 'conversation_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function reads(): HasMany
    {
        return $this->hasMany(ProjectMessageRead::class, 'project_message_id');
    }

    public function isReadBy(User|int $user): bool
    {
        $userId = $user instanceof User ? $user->id : $user;
        
        // Sender automatically has read their own message
        if ((int) $this->sender_id === (int) $userId) {
            return true;
        }

        if ($this->relationLoaded('reads')) {
            return $this->reads->contains('user_id', $userId);
        }

        return $this->reads()->where('user_id', $userId)->exists();
    }

    public function markAsReadBy(User|int $user): void
    {
        $userId = $user instanceof User ? $user->id : $user;

        if ((int) $this->sender_id === (int) $userId) {
            return;
        }

        $this->reads()->firstOrCreate(
            ['user_id' => $userId],
            ['read_at' => now()]
        );
    }
}
