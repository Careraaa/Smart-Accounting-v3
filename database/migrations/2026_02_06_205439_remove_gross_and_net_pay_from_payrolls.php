<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn(['gross_pay', 'net_pay']);
        });
    }

    public function down()
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->decimal('gross_pay', 10, 2)->default(0);
            $table->decimal('net_pay', 10, 2)->default(0);
        });
    }
};
