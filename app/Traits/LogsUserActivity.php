<?php

namespace App\Traits;

use App\Models\UserActivity;

trait LogsUserActivity
{
    protected function logActivity(
        string $action,
        string $subjectLabel,
        ?string $url = null,
        ?string $subjectType = null,
        ?int $subjectId = null
    ): void {
        if (!auth()->check()) return;

        UserActivity::create([
            'user_id'       => auth()->id(),
            'action'        => $action,
            'subject_type'  => $subjectType,
            'subject_id'    => $subjectId,
            'subject_label' => $subjectLabel,
            'url'           => $url ?? request()->url(),
        ]);
    }
}
