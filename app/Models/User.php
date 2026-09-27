<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'status',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'status' => UserStatus::class,
        ];
    }

    /**
     * Client profile relationship.
     */
    public function clientProfile(): HasOne
    {
        return $this->hasOne(ClientProfile::class, 'user_id');
    }

    /**
     * Client projects relationship.
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'client_id');
    }

    /**
     * Client leads relationship.
     */
    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class, 'client_id');
    }

    /**
     * Assigned leads relationship for staff.
     */
    public function assignedLeads(): HasMany
    {
        return $this->hasMany(Lead::class, 'assigned_user_id');
    }

    /**
     * Client quotations relationship.
     */
    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class, 'client_id');
    }

    /**
     * Client invoices relationship.
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'client_id');
    }

    /**
     * Client payments relationship.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'client_id');
    }

    /**
     * Client documents relationship.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'client_id');
    }

    /**
     * Support tickets created by client.
     */
    public function supportTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class, 'client_id');
    }

    /**
     * Support tickets assigned to staff member.
     */
    public function assignedSupportTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class, 'assigned_to_id');
    }

    /**
     * Change requests submitted by client.
     */
    public function changeRequests(): HasMany
    {
        return $this->hasMany(ChangeRequest::class, 'client_id');
    }

    /**
     * Change requests reviewed by staff member.
     */
    public function reviewedChangeRequests(): HasMany
    {
        return $this->hasMany(ChangeRequest::class, 'reviewed_by_id');
    }

    /**
     * Check if user has administrative privileges (admin or super_admin).
     */
    public function isAdmin(): bool
    {
        return $this->role instanceof UserRole
            ? $this->role->isAdminRole()
            : in_array($this->role, [UserRole::ADMIN->value, UserRole::SUPER_ADMIN->value], true);
    }

    /**
     * Check if user is support staff or admin.
     */
    public function isSupportStaff(): bool
    {
        return $this->isAdmin() || $this->hasRole(UserRole::SUPPORT->value);
    }

    /**
     * Check if user is a client account.
     */
    public function isClient(): bool
    {
        return $this->role instanceof UserRole
            ? $this->role->isClientRole()
            : $this->role === UserRole::CLIENT->value;
    }

    /**
     * Check if user account is active.
     */
    public function isActive(): bool
    {
        return $this->status instanceof UserStatus
            ? $this->status === UserStatus::ACTIVE
            : $this->status === UserStatus::ACTIVE->value;
    }

    /**
     * Determine if the user has any of the specified roles.
     */
    public function hasRole(UserRole|string ...$roles): bool
    {
        $userRoleValue = $this->role instanceof UserRole ? $this->role->value : (string) $this->role;

        foreach ($roles as $role) {
            $checkValue = $role instanceof UserRole ? $role->value : (string) $role;
            if ($userRoleValue === $checkValue) {
                return true;
            }
            if ($checkValue === 'admin' && $this->isAdmin()) {
                return true;
            }
        }

        return false;
    }
}
