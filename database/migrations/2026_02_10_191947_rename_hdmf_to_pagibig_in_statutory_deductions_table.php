<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        DB::table('statutory_deductions')
            ->where('name', 'HDMF')
            ->update(['name' => 'Pag-IBIG']);
    }

    public function down()
    {
        DB::table('statutory_deductions')
            ->where('name', 'Pag-IBIG')
            ->update(['name' => 'HDMF']);
    }
};
