<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('slug_redirects', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type', 20);
            $table->string('old_slug', 190);
            $table->unsignedBigInteger('target_id');
            $table->timestamps();
            $table->unique(['entity_type', 'old_slug']);
            $table->index(['entity_type', 'target_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('slug_redirects');
    }
};
