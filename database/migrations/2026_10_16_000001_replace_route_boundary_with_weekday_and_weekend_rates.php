<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $hasLegacyBoundary = Schema::hasColumn('routes', 'boundary');

        Schema::table('routes', function (Blueprint $table) {
            if (! Schema::hasColumn('routes', 'weekday_boundary')) {
                $table->decimal('weekday_boundary', 10, 2)->default(0)->after('route_name');
            }
            if (! Schema::hasColumn('routes', 'weekend_boundary')) {
                $table->decimal('weekend_boundary', 10, 2)->default(0)->after('weekday_boundary');
            }
        });

        if ($hasLegacyBoundary) {
            DB::table('routes')->update([
                'weekday_boundary' => DB::raw('COALESCE(boundary, 0)'),
                'weekend_boundary' => DB::raw('COALESCE(boundary, 0)'),
            ]);

            Schema::table('routes', function (Blueprint $table) {
                $table->dropColumn('boundary');
            });
        }
    }

    public function down(): void
    {
        Schema::table('routes', function (Blueprint $table) {
            if (! Schema::hasColumn('routes', 'boundary')) {
                $table->decimal('boundary', 10, 2)->nullable()->after('route_name');
            }
        });

        if (Schema::hasColumn('routes', 'weekday_boundary')) {
            DB::table('routes')->update([
                'boundary' => DB::raw('COALESCE(weekday_boundary, 0)'),
            ]);
        }

        Schema::table('routes', function (Blueprint $table) {
            $columns = array_values(array_filter(
                ['weekday_boundary', 'weekend_boundary'],
                fn (string $column) => Schema::hasColumn('routes', $column)
            ));

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
