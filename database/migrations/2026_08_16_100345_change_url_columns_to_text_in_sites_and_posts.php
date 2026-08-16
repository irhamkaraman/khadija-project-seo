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
        Schema::table('sites', function (Blueprint $table) {
            $table->text('url')->change();
            $table->text('image_url')->nullable()->change();
            $table->text('share_link')->change();
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->text('image_url')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->string('url')->change();
            $table->string('image_url')->nullable()->change();
            $table->string('share_link')->change();
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->string('image_url')->nullable()->change();
        });
    }
};
