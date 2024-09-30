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
        Schema::create('email_messages', function (Blueprint $table) {
            $table->id();

            $table->string('message_id')->nullable();
            $table->text('subject')->nullable();
            $table->string('from')->nullable();
            $table->text('to')->nullable();
            $table->text('cc')->nullable();
            $table->text('bcc')->nullable();
            $table->text('reply_to')->nullable();
            $table->dateTime('date')->nullable();
            $table->text('body_text')->nullable();
            $table->text('body_html')->nullable();
            $table->boolean('is_seen')->default(false);
            $table->boolean('is_flagged')->default(false);
            $table->boolean('is_answered')->default(false);
            $table->string('folder')->nullable();
            $table->foreignId('reply_to_email_message_id')
                ->nullable()
                ->constrained('email_messages')
                ->nullOnDelete();

            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_messages');
    }
};
