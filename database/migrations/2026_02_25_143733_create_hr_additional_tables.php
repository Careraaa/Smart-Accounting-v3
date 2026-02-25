<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1️⃣ Employee Experiences (numbered list)
        Schema::create('employee_experiences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('company_name');
            $table->string('position')->nullable();
            $table->string('duration')->nullable();
            $table->text('responsibilities')->nullable();
            $table->integer('sequence')->default(1); // numbered
            $table->timestamps();
        });

        // 2️⃣ Employee Skills (numbered list)
        Schema::create('employee_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('skill_name');
            $table->string('proficiency')->nullable();
            $table->integer('sequence')->default(1); // numbered
            $table->timestamps();
        });

        // 3️⃣ Employee Beneficiaries (numbered list)
        Schema::create('employee_beneficiaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('name');
            $table->date('date_of_birth')->nullable();
            $table->string('relationship')->nullable();
            $table->integer('sequence')->default(1); // numbered
            $table->timestamps();
        });

        // 4️⃣ Employee Character References (numbered list)
        Schema::create('employee_references', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('name');
            $table->string('address')->nullable();
            $table->string('contact_number')->nullable();
            $table->integer('sequence')->default(1); // numbered
            $table->timestamps();
        });

        // 5️⃣ Employee Attachments (checklist)
        Schema::create('employee_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('attachment_type'); // e.g., drivers_license, 2x2_picture, NBI, etc.
            $table->string('file_path')->nullable();
            $table->boolean('provided')->default(false); // checked or not
            $table->timestamps();
        });

        // 6️⃣ Add driver license columns to employees
        Schema::table('employees', function (Blueprint $table) {
            $table->string('driver_license_number')->nullable()->after('educational_attainment');
            $table->date('driver_license_validity')->nullable()->after('driver_license_number');
        });
    }

    public function down(): void
    {
        // Drop tables in reverse order
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['driver_license_number', 'driver_license_validity']);
        });

        Schema::dropIfExists('employee_attachments');
        Schema::dropIfExists('employee_references');
        Schema::dropIfExists('employee_beneficiaries');
        Schema::dropIfExists('employee_skills');
        Schema::dropIfExists('employee_experiences');
    }
};
