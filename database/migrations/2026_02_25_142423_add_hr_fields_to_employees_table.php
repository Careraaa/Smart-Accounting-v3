<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {

            $table->string('middle_name')->nullable()->after('first_name');
            $table->string('civil_status')->nullable()->after('address');
            $table->string('spouse_name')->nullable()->after('civil_status');
            $table->string('place_of_birth')->nullable()->after('date_of_birth');
            $table->string('educational_attainment')->nullable()->after('place_of_birth');

            $table->string('sss_number')->nullable()->after('has_sss');
            $table->string('tin_number')->nullable()->after('sss_number');
            $table->string('pagibig_number')->nullable()->after('tin_number');

            $table->string('signature_path')->nullable()->after('pagibig_number');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'middle_name',
                'civil_status',
                'spouse_name',
                'place_of_birth',
                'educational_attainment',
                'sss_number',
                'tin_number',
                'pagibig_number',
                'signature_path',
            ]);
        });
    }
};
