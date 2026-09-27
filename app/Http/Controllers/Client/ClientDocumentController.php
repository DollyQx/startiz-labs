<?php

namespace App\Http\Controllers\Client;

use App\Enums\DocumentType;
use App\Enums\DocumentVisibility;
use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\View\View;

class ClientDocumentController extends Controller
{
    /**
     * Display a listing of client's accessible documents.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Document::class);

        // Strict DB-level filter: only authenticated client's CLIENT_VISIBLE documents
        $query = Document::with('project')
            ->where('client_id', auth()->id())
            ->where('visibility', DocumentVisibility::CLIENT->value);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('original_filename', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('document_type')) {
            $query->where('document_type', $request->input('document_type'));
        }

        $documents = $query->latest()->paginate(15)->withQueryString();

        return view('client.documents.index', [
            'documents' => $documents,
            'documentTypes' => DocumentType::cases(),
        ]);
    }

    /**
     * Display the specified document details to client.
     */
    public function show(Document $document): View
    {
        Gate::authorize('view', $document);

        $document->load('project');

        return view('client.documents.show', [
            'document' => $document,
        ]);
    }

    /**
     * Download the specified client document.
     */
    public function download(Document $document): StreamedResponse|RedirectResponse
    {
        Gate::authorize('download', $document);

        if (! Storage::disk('local')->exists($document->storage_path)) {
            return back()->with('error', 'The requested file could not be found.');
        }

        ActivityLogger::log(
            'document_downloaded',
            $document,
            "Client downloaded document {$document->reference_number}",
            ['original_filename' => $document->original_filename]
        );

        return Storage::disk('local')->download(
            $document->storage_path,
            $document->original_filename,
            ['Content-Type' => $document->mime_type ?? 'application/octet-stream']
        );
    }
}
