<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectConversation;
use App\Models\ProjectMessage;
use App\Services\ActivityLogger;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminProjectMessageController extends Controller
{
    /**
     * Display the project communication thread for Admin/Staff.
     */
    public function index(Request $request, Project $project): View
    {
        Gate::authorize('view', $project);

        $conversation = ProjectConversation::firstOrCreate([
            'project_id' => $project->id,
        ]);

        $messages = ProjectMessage::with(['sender', 'reads'])
            ->where('project_id', $project->id)
            ->orderBy('created_at', 'asc')
            ->paginate(30);

        // Mark unread messages as read for authenticated admin
        $user = $request->user();
        foreach ($messages as $msg) {
            if (!$msg->isReadBy($user)) {
                $msg->markAsReadBy($user);
            }
        }

        return view('admin.projects.messages', [
            'project' => $project,
            'conversation' => $conversation,
            'messages' => $messages,
        ]);
    }

    /**
     * Post a new project message as Admin/Staff.
     */
    public function store(Request $request, Project $project): RedirectResponse
    {
        Gate::authorize('view', $project);

        $request->validate([
            'message' => 'nullable|string|max:5000|required_without:attachment',
            'attachment' => 'nullable|file|max:10240|required_without:message',
        ], [
            'message.required_without' => 'Please provide a message or an attachment.',
            'attachment.required_without' => 'Please provide a message or an attachment.',
            'attachment.max' => 'The attachment size must not exceed 10MB.',
        ]);

        // Validate safe file extensions if attachment present
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $ext = strtolower($file->getClientOriginalExtension());
            $blockedExtensions = ['php', 'phtml', 'php5', 'exe', 'bat', 'cmd', 'sh', 'com', 'dll', 'jar', 'vbs', 'js', 'html', 'htm'];

            if (in_array($ext, $blockedExtensions, true)) {
                return back()->withErrors(['attachment' => 'The uploaded file type is restricted for security reasons.']);
            }
        }

        $conversation = ProjectConversation::firstOrCreate([
            'project_id' => $project->id,
        ]);

        $attachmentPath = null;
        $originalFilename = null;
        $mimeType = null;
        $fileSize = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $originalFilename = $file->getClientOriginalName();
            $mimeType = $file->getClientMimeType();
            $fileSize = $file->getSize();
            $attachmentPath = $file->store('project_messages', 'local');
        }

        $message = ProjectMessage::create([
            'conversation_id' => $conversation->id,
            'project_id' => $project->id,
            'sender_id' => $request->user()->id,
            'message' => $request->input('message'),
            'attachment_path' => $attachmentPath,
            'original_filename' => $originalFilename,
            'mime_type' => $mimeType,
            'file_size' => $fileSize,
        ]);

        $conversation->update(['last_message_at' => now()]);

        // Auto-mark read by sender
        $message->markAsReadBy($request->user());

        // Notify client
        if ($project->client) {
            NotificationService::notifyUser(
                $project->client,
                'project_message',
                'New Message on ' . $project->title,
                $request->user()->name . ' sent a message on project ' . $project->reference_number,
                route('client.projects.messages', $project),
                $project
            );
        }

        // Activity log
        ActivityLogger::log(
            'project.message_sent',
            $project,
            'Staff sent a message on project ' . $project->reference_number,
            [
                'message_id' => $message->id,
                'has_attachment' => !empty($attachmentPath),
            ],
            $request->user()->id
        );

        if ($attachmentPath) {
            ActivityLogger::log(
                'project.message_attachment_uploaded',
                $project,
                'Staff uploaded attachment (' . $originalFilename . ') on project ' . $project->reference_number,
                [
                    'message_id' => $message->id,
                    'filename' => $originalFilename,
                ],
                $request->user()->id
            );
        }

        return redirect()->route('admin.projects.messages', $project)
            ->with('status', 'Message sent successfully.');
    }

    /**
     * Download a private message attachment.
     */
    public function downloadAttachment(Request $request, Project $project, ProjectMessage $message): StreamedResponse|RedirectResponse
    {
        Gate::authorize('view', $project);

        if ((int) $message->project_id !== (int) $project->id) {
            abort(404, 'Message attachment not found for this project.');
        }

        if (empty($message->attachment_path) || !Storage::disk('local')->exists($message->attachment_path)) {
            abort(404, 'Attachment file does not exist.');
        }

        ActivityLogger::log(
            'project.message_attachment_downloaded',
            $project,
            'Downloaded message attachment: ' . $message->original_filename,
            ['message_id' => $message->id],
            $request->user()->id
        );

        return Storage::disk('local')->download(
            $message->attachment_path,
            $message->original_filename
        );
    }
}
