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
        Schema::create('rm_bulk_sms', function (Blueprint $table) {
            $table->id();
            $table->string('to');
            $table->string('sender')->nullable();
            $table->text('message');
            $table->string('message_id')->nullable();
            // $table->enum('status', ['queued', 'sent', 'delivered', 'failed'])->default('queued');
            $table->string('status')->nullable();
            $table->json('response')->nullable();

            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rm_bulk_sms');
    }
};
