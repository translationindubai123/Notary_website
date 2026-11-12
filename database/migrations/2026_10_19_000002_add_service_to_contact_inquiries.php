<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('contact_inquiries', function (Blueprint $table) {
            $table->foreignId('service_id')->nullable()->after('phone')->constrained('services')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('contact_inquiries', function (Blueprint $table) {
            $table->dropConstrainedForeignId('service_id');
        });
    }
};
