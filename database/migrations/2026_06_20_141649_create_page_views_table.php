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
        Schema::create('page_views', function (Blueprint $table) {
            $table->id();
            $table->uuid('visitor_key');
            $table->string('route_name');
            $table->string('path');
            $table->string('blog_slug')->nullable();
            $table->timestamp('viewed_at');
            $table->index('visitor_key');
            $table->index('route_name');
            $table->index('blog_slug');
            $table->index('viewed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_views');
    }
};
