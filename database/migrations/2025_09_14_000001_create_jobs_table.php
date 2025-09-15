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
        Schema::create('trigger_jobs', function (Blueprint $table) {
            $table->id();

            // Unique identifier for the trigger job
            $table->uuid('uuid')->unique();

            // Polymorphic relation to billable model (user/account/etc.)
            $table->string('billable_type');
            $table->unsignedBigInteger('billable_id');


            // Status of the job/event
            $table->enum('status', ['pending', 'running', 'completed', 'failed'])
                ->default('pending');

            // External Trigger.dev / Inngest job ID
            $table->string('run_id')->unique();

            // Optional: store arbitrary job data
            $table->json('payload')->nullable();

            $table->timestamps();

            // Indexes for fast lookups
            $table->index(['billable_type', 'billable_id']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trigger_jobs');
    }
};
