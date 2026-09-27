<?php

namespace App\Enums;

enum ProjectSignOffStatus: string
{
    case PENDING_REVIEW = 'pending_review';
    case FEEDBACK_REQUIRED = 'feedback_required';
    case REVISION_IN_PROGRESS = 'revision_in_progress';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::PENDING_REVIEW => 'Pending Client Review',
            self::FEEDBACK_REQUIRED => 'Feedback / Revisions Requested',
            self::REVISION_IN_PROGRESS => 'Revision In Progress',
            self::APPROVED => 'Approved & Accepted',
            self::REJECTED => 'Rejected',
        };
    }
}
