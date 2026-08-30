<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Remap legacy/seeded attachment keys to the current real document types.
     * Old seeders used keys like 'drivers_license' that never matched the
     * production attachmentTypes() list, so those documents showed as
     * "Missing" while the summary cards still counted them.
     */
    public function up(): void
    {
        $remap = [
            'medical_cert'       => ['medical_certificate', 'Medical Certificate'],
            'valid_id_1'         => ['government_id', 'Valid Government ID (Front & Back)'],
            'drivers_license'    => ['government_id', 'Valid Government ID (Front & Back)'],
            'barangay_clearance' => ['nbi_clearance', 'NBI Clearance'],
        ];

        foreach ($remap as $old => [$newKey, $newLabel]) {
            DB::table('employee_attachments')
                ->where('attachment_key', $old)
                ->update([
                    'attachment_key' => $newKey,
                    'label'          => $newLabel,
                ]);
        }
    }

    public function down(): void
    {
        // Reverse mapping is intentionally omitted; remapping back would
        // be ambiguous for keys that merged into 'government_id'.
    }
};
