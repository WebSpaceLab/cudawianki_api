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
        Schema::create('inboxes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // tutaj za jakiś czas można użyć tych pól do skrzynki mailowej
            // sender_id - pole typu INTEGER, służące do przechowywania ID nadawcy wiadomości.
            // recipient_id - pole typu INTEGER, służące do przechowywania ID odbiorcy wiadomości.

            $table->string('subject');
            $table->string('sender');
            $table->string('email');
            $table->string('phone');

            $table->text('content');
            $table->boolean('is_read');

            // $table->binary('attachment');
            // $table->string('attachment_filename');
            // $table->string('attachment_mime_type');

            $table->timestamps();
            // $table->date('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inboxes');
    }
};
