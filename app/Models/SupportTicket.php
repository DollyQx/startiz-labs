<?php

namespace App\Models;

use App\Enums\TicketCategory;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Services\ReferenceNumberGenerator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_number',
        'client_id',
        'project_id',
        'subject',
        'description',
        'category',
        'priority',
        'status',
        'assigned_to_id',
        'resolved_at',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => TicketStatus::class,
            'priority' => TicketPriority::class,
            'category' => TicketCategory::class,
            'resolved_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (SupportTicket $ticket) {
            if (empty($ticket->reference_number)) {
                $ticket->reference_number = ReferenceNumberGenerator::generate('support_tickets', 'STZ-TKT');
            }
        });
    }

    /**
     * Client owner of the ticket.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Assigned staff/admin user.
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_id');
    }

    /**
     * Associated project (optional).
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    /**
     * All ticket messages (for admin/staff).
     */
    public function messages(): HasMany
    {
        return $this->hasMany(SupportTicketMessage::class, 'ticket_id')->orderBy('created_at', 'asc');
    }

    /**
     * Client-visible messages only (excludes internal notes).
     */
    public function clientVisibleMessages(): HasMany
    {
        return $this->hasMany(SupportTicketMessage::class, 'ticket_id')
            ->where('is_internal', false)
            ->orderBy('created_at', 'asc');
    }
}
