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
            $table->enum('status', [
                'queued',     // accepted by RM
                'sent',       // sent to operator
                'delivered',  // delivered to handset
                'failed'      // any failure
            ])->default('queued');
            $table->string('status_code')->nullable();  // RM raw status code from response like 1701 from 1701|8801680611205|sdfsdf-sdfsdf-dfgefg
            $table->string('response')->nullable();
            $table->text('gateway_error')->nullable(); // new column for exceptions/errors

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
