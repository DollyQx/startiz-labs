<?php

namespace App\Models;

use App\Enums\DocumentType;
use App\Enums\DocumentVisibility;
use App\Services\ReferenceNumberGenerator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_number',
        'client_id',
        'project_id',
        'uploaded_by_id',
        'document_type',
        'original_filename',
        'storage_path',
        'mime_type',
        'file_size',
        'visibility',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'document_type' => DocumentType::class,
            'visibility' => DocumentVisibility::class,
            'file_size' => 'integer',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Document $document) {
            if (empty($document->reference_number)) {
                $document->reference_number = ReferenceNumberGenerator::generate('documents', 'STZ-DOC');
            }
        });
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by_id');
    }
}
