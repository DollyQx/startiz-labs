<?php

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
        Schema::create('project_sign_offs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->unique()->constrained('projects')->cascadeOnDelete();
            $table->foreignId('client_id')->constrained('users')->cascadeOnDelete();
            $table->string('status')->default('pending_review');
            $table->foreignId('requested_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('accepted_at')->nullable();
            $table->foreignId('accepted_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('final_delivery_at')->nullable();
            $table->foreignId('final_delivery_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('final_delivery_notes')->nullable();
            $table->timestamps();

            $table->index(['project_id', 'status']);
            $table->index('client_id');
        });

        Schema::create('project_review_iterations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_sign_off_id')->constrained('project_sign_offs')->cascadeOnDelete();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->unsignedInteger('iteration_number')->default(1);
            $table->string('status')->default('pending_review');
            $table->text('review_notes')->nullable();
            $table->text('client_feedback')->nullable();
            $table->text('revision_request')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();

            $table->index(['project_sign_off_id', 'iteration_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_review_iterations');
        Schema::dropIfExists('project_sign_offs');
    }
};
