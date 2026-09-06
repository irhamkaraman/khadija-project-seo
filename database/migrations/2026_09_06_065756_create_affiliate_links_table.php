<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affiliate_links', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('affiliate_url');
            $table->text('source_url')->nullable();
            $table->text('image_url')->nullable();
            $table->text('description')->nullable();
            $table->string('badge')->nullable();
            $table->string('cta_text')->default('Lihat Penawaran');
            $table->unsignedBigInteger('click_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('display_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_links');
    }
};
