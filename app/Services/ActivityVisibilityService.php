<?php

namespace App\Services;

use App\Enums\DocumentVisibility;
use App\Models\ActivityLog;
use App\Models\Document;
use Illuminate\Database\Eloquent\Builder;

class ActivityVisibilityService
{
    /**
     * Actions that are strictly internal and must never be shown to clients.
     */
    protected static array $internalActions = [
        'ticket.internal_note_added',
        'lead.created',
        'lead.updated',
        'lead.status_updated',
        'lead.converted',
        'user.created',
        'user.updated',
        'user.deleted',
        'user.role_updated',
        'project.viewed',
        'document_deleted',
    ];

    /**
     * Actions that are safe and relevant for client project activity timelines.
     */
    protected static array $clientVisibleActions = [
        'project.created',
        'project.updated',
        'project.status_updated',
        'project.status_changed',
        'requirement.created',
        'requirement.updated',
        'milestone.created',
        'milestone.updated',
        'milestone.completed',
        'task.created',
        'task.status_updated',
        'task.completed',
        'quotation.created',
        'quotation.sent',
        'quotation.accepted',
        'quotation.rejected',
        'invoice.created',
        'invoice.status_updated',
        'invoice.sent',
        'payment.recorded',
        'payment.verified',
        'payment.received',
        'ticket.created',
        'ticket.replied',
        'ticket.status_updated',
        'ticket.resolved',
        'ticket.closed',
        'change_request.created',
        'change_request.reviewed',
        'change_request.approved',
        'change_request.rejected',
        'change_request.cancelled',
        'document_uploaded',
        'document_visibility_changed',
        'project.message_sent',
        'project.message_attachment_uploaded',
        'project.message_attachment_downloaded',
        'project.review_requested',
        'project.feedback_submitted',
        'project.client_approved',
        'project.final_delivery_recorded',
    ];

    /**
     * Determine if a given activity log entry is safe for client viewing.
     */
    public static function isClientVisible(ActivityLog $log): bool
    {
        // 1. Check strict internal actions blocklist
        if (in_array($log->action, self::$internalActions, true)) {
            return false;
        }

        // 2. Check Document visibility
        if ($log->subject_type === Document::class) {
            $document = $log->subject;
            if ($document && $document->visibility !== DocumentVisibility::CLIENT && $document->visibility !== DocumentVisibility::PUBLIC) {
                return false;
            }
        }

        // 3. Match against allowlist
        return in_array($log->action, self::$clientVisibleActions, true);
    }

    /**
     * Apply client visibility filter to an Eloquent query builder.
     */
    public static function applyClientVisibilityScope(Builder $query): Builder
    {
        return $query->whereNotIn('action', self::$internalActions)
            ->where(function ($q) {
                // Keep actions that match client-visible list
                $q->whereIn('action', self::$clientVisibleActions);
            })
            ->where(function ($q) {
                // Ensure document activities only show client-visible documents
                $q->whereNull('subject_type')
                  ->orWhere('subject_type', '!=', Document::class)
                  ->orWhereExists(function ($sub) {
                      $sub->select(\Illuminate\Support\Facades\DB::raw(1))
                          ->from('documents')
                          ->whereColumn('documents.id', 'activity_logs.subject_id')
                          ->whereIn('documents.visibility', [DocumentVisibility::CLIENT->value, DocumentVisibility::PUBLIC->value]);
                  });
            });
    }

    /**
     * Get a user-friendly label for an action name.
     */
    public static function getActionLabel(string $action): string
    {
        return match ($action) {
            'project.created' => 'Project Created',
            'project.updated' => 'Project Updated',
            'project.status_updated', 'project.status_changed' => 'Project Status Changed',
            'requirement.created' => 'Requirement Added',
            'requirement.updated' => 'Requirement Updated',
            'milestone.created' => 'Milestone Created',
            'milestone.updated' => 'Milestone Updated',
            'milestone.completed' => 'Milestone Completed',
            'task.created' => 'Task Created',
            'task.status_updated' => 'Task Status Updated',
            'task.completed' => 'Task Completed',
            'quotation.created' => 'Quotation Drafted',
            'quotation.sent' => 'Quotation Issued',
            'quotation.accepted' => 'Quotation Accepted',
            'quotation.rejected' => 'Quotation Rejected',
            'invoice.created' => 'Invoice Issued',
            'invoice.status_updated' => 'Invoice Status Updated',
            'payment.recorded', 'payment.verified', 'payment.received' => 'Payment Received',
            'ticket.created' => 'Support Ticket Submitted',
            'ticket.replied' => 'Support Ticket Reply',
            'ticket.internal_note_added' => 'Internal Note Added',
            'ticket.status_updated' => 'Ticket Status Updated',
            'ticket.resolved' => 'Ticket Resolved',
            'ticket.closed' => 'Ticket Closed',
            'ticket.assigned' => 'Ticket Assigned',
            'change_request.created' => 'Change Request Submitted',
            'change_request.reviewed' => 'Change Request Reviewed',
            'change_request.approved' => 'Change Request Approved',
            'change_request.rejected' => 'Change Request Rejected',
            'change_request.cancelled' => 'Change Request Cancelled',
            'document_uploaded' => 'Document Shared',
            'document_visibility_changed' => 'Document Visibility Updated',
            'document_downloaded' => 'Document Downloaded',
            'document_deleted' => 'Document Removed',
            'project.message_sent' => 'Project Message Sent',
            'project.message_attachment_uploaded' => 'Message Attachment Uploaded',
            'project.message_attachment_downloaded' => 'Message Attachment Downloaded',
            'project.review_requested' => 'Client Review Requested',
            'project.feedback_submitted' => 'Client Review Feedback Submitted',
            'project.client_approved' => 'Project Final Approval & Acceptance',
            'project.final_delivery_recorded' => 'Project Final Delivery Recorded',
            default => ucfirst(str_replace(['.', '_'], ' ', $action)),
        };
    }
}
