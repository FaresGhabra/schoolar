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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->text('title');
            $table->text('description');
            $table->text('thumb')->nullable();
            $table->bigInteger('price')->default(0);
            $table->text('tags')->nullable();
            $table->text('subtitle')->nullable();
            $table->text('author');
            $table->integer('duration')->default(0);
            $table->text('folder')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
