<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('service_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('service_categories')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('short_description', 500)->nullable();
            $table->text('description')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 320)->nullable();
            $table->string('canonical_url')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(false);
            $table->timestamps();
            $table->index(['is_active', 'sort_order']);
        });

        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_category_id')->constrained('service_categories')->restrictOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('short_description', 500)->nullable();
            $table->longText('introduction')->nullable();
            $table->longText('description')->nullable();
            $table->longText('eligibility')->nullable();
            $table->string('processing_time')->nullable();
            $table->string('service_availability')->nullable();
            $table->longText('important_notes')->nullable();
            $table->longText('who_needs_service')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 320)->nullable();
            $table->string('canonical_url')->nullable();
            $table->string('og_title')->nullable();
            $table->string('og_description', 320)->nullable();
            $table->string('featured_image')->nullable();
            $table->string('image_alt')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->index(['is_active', 'published_at', 'sort_order']);
            $table->index(['service_category_id', 'is_active']);
        });

        Schema::create('service_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->string('document_name');
            $table->text('document_description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['service_id', 'sort_order']);
        });

        Schema::create('service_process_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->string('step_title');
            $table->text('step_description');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['service_id', 'sort_order']);
        });

        Schema::create('service_faqs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->string('question');
            $table->text('answer');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['service_id', 'sort_order']);
        });

        Schema::create('service_related_services', function (Blueprint $table) {
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->foreignId('related_service_id')->constrained('services')->cascadeOnDelete();
            $table->timestamps();
            $table->primary(['service_id', 'related_service_id']);
            $table->index('related_service_id');
        });

        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('setting_key')->unique();
            $table->text('setting_value')->nullable();
            $table->timestamps();
        });

        Schema::create('contact_inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('subject')->nullable();
            $table->text('message');
            $table->timestamps();
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('contact_inquiries');
        Schema::dropIfExists('site_settings');
        Schema::dropIfExists('service_related_services');
        Schema::dropIfExists('service_faqs');
        Schema::dropIfExists('service_process_steps');
        Schema::dropIfExists('service_documents');
        Schema::dropIfExists('services');
        Schema::dropIfExists('service_categories');
    }
};
