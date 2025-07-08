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
        Schema::create('examples', function (Blueprint $table) {
            $table->id();

            $table->string('image');
            $table->json('albums')->nullable();
            $table->date('date');
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');

            $table->text('description')->nullable();
            $table->longText('content')->nullable();
            $table->integer('views')->default(0);
            $table->boolean('is_active')->default(true);
            $table->decimal('price', 10, 2)->nullable();

            $table->unsignedBigInteger('published')->default(2);
            $table->unsignedBigInteger('archive')->default(2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('examples');
    }
};
