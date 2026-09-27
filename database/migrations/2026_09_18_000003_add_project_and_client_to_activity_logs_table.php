<?php

use App\Models\ActivityLog;
use App\Models\Project;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            if (! Schema::hasColumn('activity_logs', 'project_id')) {
                $table->foreignId('project_id')->nullable()->after('subject_id')->constrained('projects')->nullOnDelete();
            }
            if (! Schema::hasColumn('activity_logs', 'client_id')) {
                $table->foreignId('client_id')->nullable()->after('project_id')->constrained('users')->nullOnDelete();
            }
        });

        // Database-agnostic backfill using Eloquent chunking
        ActivityLog::whereNull('project_id')->chunkById(100, function ($logs) {
            foreach ($logs as $log) {
                $projectId = $log->metadata['project_id'] ?? null;
                $clientId = $log->metadata['client_id'] ?? null;

                if (! $projectId && $log->subject_type === Project::class && $log->subject_id) {
                    $project = Project::find($log->subject_id);
                    if ($project) {
                        $projectId = $project->id;
                        $clientId = $clientId ?? $project->client_id;
                    }
                }

                if ($projectId || $clientId) {
                    $log->update([
                        'project_id' => $projectId,
                        'client_id' => $clientId,
                    ]);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            if (Schema::hasColumn('activity_logs', 'project_id')) {
                $table->dropForeign(['project_id']);
                $table->dropColumn(['project_id']);
            }
            if (Schema::hasColumn('activity_logs', 'client_id')) {
                $table->dropForeign(['client_id']);
                $table->dropColumn(['client_id']);
            }
        });
    }
};
