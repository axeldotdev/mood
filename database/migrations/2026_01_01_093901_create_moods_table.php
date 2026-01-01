<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('moods', function (Blueprint $table): void {
            $table->id();
            $table->json('types');
            $table->text('comment')->nullable();
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
        });
    }
};
