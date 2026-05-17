<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Bonus extends Model
{
    public const TYPE_FIXED = 'fixed_amount';

    public const TYPE_PERCENTAGE = 'percentage_based';

    public const TYPE_FORMULA = 'formula_based';

    public const TYPE_MANDATORY = 'mandatory_bonus';

    public const METHOD_MANUAL = 'manual_amount';

    public const METHOD_AUTO = 'automatic_formula';

    public const CODE_THIRTEENTH_MONTH = 'thirteenth_month_pay';

    protected $fillable = [
        'name',
        'description',
        'type',
        'computation_method',
        'formula',
        'fixed_amount',
        'percentage_value',
        'is_mandatory',
        'is_system_generated',
        'status',
        'year',
        'payroll_period',
        'code',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'fixed_amount' => 'decimal:2',
            'percentage_value' => 'decimal:4',
            'is_mandatory' => 'boolean',
            'is_system_generated' => 'boolean',
            'year' => 'integer',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function eligibleEmployees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'bonus_employee', 'bonus_id', 'user_id');
    }

    public function isThirteenthMonthPay(): bool
    {
        return $this->code === self::CODE_THIRTEENTH_MONTH;
    }

    public function isDeletable(): bool
    {
        return ! $this->is_mandatory && ! $this->is_system_generated;
    }

    public function isEditableBy(User $user): bool
    {
        if ($this->is_mandatory && $this->is_system_generated) {
            return $user->role === 'superadmin';
        }

        return in_array($user->role, ['hr', 'superadmin'], true);
    }

    public function hasManagePage(): bool
    {
        return $this->isThirteenthMonthPay();
    }

    public function manageUrl(): ?string
    {
        if ($this->isThirteenthMonthPay()) {
            return route('payroll.thirteenth-month-pay.index', ['year' => $this->year ?? now()->year]);
        }

        return null;
    }

    public function rowUrl(): string
    {
        if ($this->hasManagePage()) {
            return $this->manageUrl();
        }

        return route('bonuses.edit', $this);
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_FIXED => 'Fixed Amount',
            self::TYPE_PERCENTAGE => 'Percentage Based',
            self::TYPE_FORMULA => 'Formula Based',
            self::TYPE_MANDATORY => 'Mandatory Bonus',
            default => ucfirst(str_replace('_', ' ', $this->type)),
        };
    }

    public function getComputationMethodLabelAttribute(): string
    {
        return match ($this->computation_method) {
            self::METHOD_MANUAL => 'Manual Amount',
            self::METHOD_AUTO => 'Automatic Formula',
            default => ucfirst(str_replace('_', ' ', $this->computation_method)),
        };
    }

    public function getAmountFormulaDisplayAttribute(): string
    {
        if ($this->computation_method === self::METHOD_MANUAL) {
            if ($this->type === self::TYPE_PERCENTAGE && $this->percentage_value !== null) {
                return number_format((float) $this->percentage_value, 2) . '%';
            }

            if ($this->fixed_amount !== null) {
                return '₱' . number_format((float) $this->fixed_amount, 2);
            }
        }

        return $this->formula ?: '—';
    }

    public function getCoveragePeriodDisplayAttribute(): string
    {
        $parts = array_filter([
            $this->payroll_period,
            $this->year ? (string) $this->year : null,
        ]);

        return $parts ? implode(' · ', $parts) : '—';
    }

    public static function typeOptions(): array
    {
        return [
            self::TYPE_FIXED => 'Fixed Amount',
            self::TYPE_PERCENTAGE => 'Percentage Based',
            self::TYPE_FORMULA => 'Formula Based',
            self::TYPE_MANDATORY => 'Mandatory Bonus',
        ];
    }

    public static function computationMethodOptions(): array
    {
        return [
            self::METHOD_MANUAL => 'Manual Amount',
            self::METHOD_AUTO => 'Automatic Formula',
        ];
    }
}
