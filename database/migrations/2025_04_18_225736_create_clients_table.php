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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('avatar_image');
            $table->unsignedInteger('country_id');
            $table->foreign('country_id')->references('id')->on('lc_countries')->onDelete('restrict');
            $table->enum('gender', ['Male', 'Female']);
            $table->string('mobile');
            $table->timestamp('last_login_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('staff')->onDelete('restrict');
            $table->timestamp('approved_at')->nullable();
            $table->string('reset_token')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
