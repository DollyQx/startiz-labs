<?php

namespace App\Http\Controllers\Admin;

use App\Enums\DocumentType;
use App\Enums\DocumentVisibility;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Project;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\View\View;

class AdminDocumentController extends Controller
{
    /**
     * Display a listing of documents.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Document::class);

        $query = Document::with(['client', 'project', 'uploadedBy']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('original_filename', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->input('client_id'));
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->input('project_id'));
        }

        if ($request->filled('document_type')) {
            $query->where('document_type', $request->input('document_type'));
        }

        if ($request->filled('visibility')) {
            $query->where('visibility', $request->input('visibility'));
        }

        $documents = $query->latest()->paginate(15)->withQueryString();

        $clients = User::where(function ($q) {
            $q->where('role', UserRole::CLIENT->value)
              ->orWhere('role', UserRole::CLIENT);
        })->orderBy('name')->get();

        $projects = Project::orderBy('title')->get();

        return view('admin.documents.index', [
            'documents' => $documents,
            'clients' => $clients,
            'projects' => $projects,
            'documentTypes' => DocumentType::cases(),
            'visibilityOptions' => DocumentVisibility::cases(),
        ]);
    }

    /**
     * Show the form for uploading a new document.
     */
    public function create(Request $request): View
    {
        Gate::authorize('create', Document::class);

        $clients = User::where(function ($q) {
            $q->where('role', UserRole::CLIENT->value)
              ->orWhere('role', UserRole::CLIENT);
        })->orderBy('name')->get();

        $projects = Project::with('client')->orderBy('title')->get();

        return view('admin.documents.create', [
            'clients' => $clients,
            'projects' => $projects,
            'documentTypes' => DocumentType::cases(),
            'visibilityOptions' => DocumentVisibility::cases(),
            'selectedClientId' => $request->query('client_id'),
            'selectedProjectId' => $request->query('project_id'),
        ]);
    }

    /**
     * Store a newly uploaded document.
     */
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', Document::class);

        $validTypes = array_map(fn ($case) => $case->value, DocumentType::cases());
        $validVisibilities = array_map(fn ($case) => $case->value, DocumentVisibility::cases());

        $request->validate([
            'client_id' => ['nullable', 'exists:users,id'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'document_type' => ['required', 'string', 'in:' . implode(',', $validTypes)],
            'visibility' => ['required', 'string', 'in:' . implode(',', $validVisibilities)],
            'description' => ['nullable', 'string', 'max:1000'],
            'file' => ['required', 'file', 'max:20480'], // 20MB max
        ]);

        if (! $request->filled('client_id') && ! $request->filled('project_id')) {
            throw ValidationException::withMessages([
                'client_id' => 'Either a client or a project must be associated with the document.',
            ]);
        }

        $clientId = $request->input('client_id');
        $projectId = $request->input('project_id');

        if ($projectId) {
            $project = Project::findOrFail($projectId);
            if ($clientId && (int) $project->client_id !== (int) $clientId) {
                throw ValidationException::withMessages([
                    'project_id' => 'The selected project does not belong to the selected client.',
                ]);
            }
            if (! $clientId) {
                $clientId = $project->client_id;
            }
        }

        $file = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension());
        $allowedExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'png', 'jpg', 'jpeg', 'zip'];

        if (! in_array($extension, $allowedExtensions, true)) {
            throw ValidationException::withMessages([
                'file' => 'The file extension .' . $extension . ' is not permitted. Allowed extensions: ' . implode(', ', $allowedExtensions),
            ]);
        }

        $originalFilename = $file->getClientOriginalName();
        $mimeType = $file->getMimeType();
        $fileSize = $file->getSize();

        // Save privately under storage/app/private/documents with randomized filename
        $storagePath = $file->store('documents', 'local');

        $document = Document::create([
            'client_id' => $clientId,
            'project_id' => $projectId,
            'uploaded_by_id' => auth()->id(),
            'document_type' => $request->input('document_type'),
            'original_filename' => $originalFilename,
            'storage_path' => $storagePath,
            'mime_type' => $mimeType,
            'file_size' => $fileSize,
            'visibility' => $request->input('visibility'),
            'description' => $request->input('description'),
        ]);

        ActivityLogger::log(
            'document_uploaded',
            $document,
            "Uploaded document {$document->reference_number} ({$document->original_filename})",
            [
                'original_filename' => $originalFilename,
                'visibility' => $document->visibility->value,
                'client_id' => $clientId,
                'project_id' => $projectId,
            ]
        );

        if ($document->visibility->value === DocumentVisibility::CLIENT->value && $document->client) {
            \App\Services\NotificationService::notifyUser(
                $document->client,
                'document',
                "New Document Shared: {$document->original_filename}",
                "A new document ({$document->original_filename}) has been shared with your account.",
                route('client.documents.show', $document->id),
                $document
            );
        }

        return redirect()
            ->route('admin.documents.show', $document)
            ->with('success', "Document {$document->reference_number} uploaded successfully.");
    }

    /**
     * Display the document operational details.
     */
    public function show(Document $document): View
    {
        Gate::authorize('view', $document);

        $document->load(['client', 'project', 'uploadedBy']);

        return view('admin.documents.show', [
            'document' => $document,
        ]);
    }

    /**
     * Download the specified document file securely.
     */
    public function download(Document $document): StreamedResponse|RedirectResponse
    {
        Gate::authorize('download', $document);

        if (! Storage::disk('local')->exists($document->storage_path)) {
            return back()->with('error', 'The requested file path does not exist on the server.');
        }

        ActivityLogger::log(
            'document_downloaded',
            $document,
            "Downloaded document {$document->reference_number}",
            ['original_filename' => $document->original_filename]
        );

        return Storage::disk('local')->download(
            $document->storage_path,
            $document->original_filename,
            ['Content-Type' => $document->mime_type ?? 'application/octet-stream']
        );
    }

    /**
     * Update the visibility of the specified document.
     */
    public function updateVisibility(Request $request, Document $document): RedirectResponse
    {
        Gate::authorize('update', $document);

        $validVisibilities = array_map(fn ($case) => $case->value, DocumentVisibility::cases());

        $request->validate([
            'visibility' => ['required', 'string', 'in:' . implode(',', $validVisibilities)],
        ]);

        $oldVisibility = $document->visibility->value;
        $newVisibility = $request->input('visibility');

        $document->update([
            'visibility' => $newVisibility,
        ]);

        ActivityLogger::log(
            'document_visibility_changed',
            $document,
            "Changed document visibility from {$oldVisibility} to {$newVisibility}",
            [
                'old_visibility' => $oldVisibility,
                'new_visibility' => $newVisibility,
            ]
        );

        if ($newVisibility === DocumentVisibility::CLIENT->value && $oldVisibility !== DocumentVisibility::CLIENT->value && $document->client) {
            \App\Services\NotificationService::notifyUser(
                $document->client,
                'document',
                "New Document Shared: {$document->original_filename}",
                "A document ({$document->original_filename}) has been made available to your account.",
                route('client.documents.show', $document->id),
                $document
            );
        }

        return back()->with('success', 'Document visibility updated successfully.');
    }

    /**
     * Remove the specified document and its stored physical file.
     */
    public function destroy(Document $document): RedirectResponse
    {
        Gate::authorize('delete', $document);

        $ref = $document->reference_number;
        $name = $document->original_filename;

        // Safely remove physical file
        if (Storage::disk('local')->exists($document->storage_path)) {
            Storage::disk('local')->delete($document->storage_path);
        }

        ActivityLogger::log(
            'document_deleted',
            $document,
            "Deleted document {$ref} ({$name})",
            ['original_filename' => $name]
        );

        $document->delete();

        return redirect()
            ->route('admin.documents.index')
            ->with('success', "Document {$ref} deleted successfully.");
    }
}
