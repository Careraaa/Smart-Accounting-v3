<?php

namespace App\Services;

use InvalidArgumentException;

class BonusFormulaEngine
{
    public const VARIABLES = [
        'basic_salary',
        'total_basic_salary',
        'months_worked',
        'gross_pay',
        'attendance_days',
    ];

    /**
     * Evaluate a payroll formula with the given variable values.
     *
     * @param  array<string, float|int>  $variables
     */
    public function evaluate(string $formula, array $variables = []): float
    {
        $expression = trim($formula);

        if ($expression === '') {
            throw new InvalidArgumentException('Formula cannot be empty.');
        }

        foreach (self::VARIABLES as $name) {
            if (! array_key_exists($name, $variables)) {
                $variables[$name] = 0;
            }
        }

        foreach ($variables as $name => $value) {
            if (! in_array($name, self::VARIABLES, true)) {
                continue;
            }

            $numeric = is_numeric($value) ? (float) $value : 0;
            $expression = preg_replace(
                '/\b' . preg_quote($name, '/') . '\b/',
                '(' . $numeric . ')',
                $expression
            );
        }

        $expression = preg_replace('/\s+/', '', $expression);

        if (! preg_match('/^[0-9+\-*\/().]+$/', $expression)) {
            throw new InvalidArgumentException('Formula contains invalid characters or unknown variables.');
        }

        try {
            $result = $this->evaluateExpression($expression);
        } catch (\Throwable $e) {
            throw new InvalidArgumentException('Unable to evaluate formula: ' . $e->getMessage());
        }

        return round((float) $result, 2);
    }

    public function validate(string $formula): bool
    {
        $this->evaluate($formula, array_fill_keys(self::VARIABLES, 1));

        return true;
    }

    public static function variableHelp(): array
    {
        return [
            'basic_salary' => 'Current period basic salary',
            'total_basic_salary' => 'Total basic salary earned in the coverage year',
            'months_worked' => 'Months worked within the coverage year',
            'gross_pay' => 'Gross pay for the period',
            'attendance_days' => 'Attendance days in the period',
        ];
    }

    private function evaluateExpression(string $expression): float
    {
        $tokens = $this->tokenize($expression);
        $position = 0;

        return $this->parseExpression($tokens, $position);
    }

    /** @return list<string> */
    private function tokenize(string $expression): array
    {
        preg_match_all('/([0-9.]+)|([+\-*\/()])/', $expression, $matches, PREG_SET_ORDER);

        $tokens = [];
        foreach ($matches as $match) {
            $tokens[] = $match[0];
        }

        return $tokens;
    }

    /** @param list<string> $tokens */
    private function parseExpression(array $tokens, int &$position): float
    {
        $value = $this->parseTerm($tokens, $position);

        while ($position < count($tokens) && in_array($tokens[$position], ['+', '-'], true)) {
            $operator = $tokens[$position++];
            $right = $this->parseTerm($tokens, $position);
            $value = $operator === '+' ? $value + $right : $value - $right;
        }

        return $value;
    }

    /** @param list<string> $tokens */
    private function parseTerm(array $tokens, int &$position): float
    {
        $value = $this->parseFactor($tokens, $position);

        while ($position < count($tokens) && in_array($tokens[$position], ['*', '/'], true)) {
            $operator = $tokens[$position++];
            $right = $this->parseFactor($tokens, $position);

            if ($operator === '/' && abs($right) < 1e-12) {
                throw new InvalidArgumentException('Division by zero.');
            }

            $value = $operator === '*' ? $value * $right : $value / $right;
        }

        return $value;
    }

    /** @param list<string> $tokens */
    private function parseFactor(array $tokens, int &$position): float
    {
        if ($position >= count($tokens)) {
            throw new InvalidArgumentException('Unexpected end of formula.');
        }

        $token = $tokens[$position];

        if ($token === '(') {
            $position++;
            $value = $this->parseExpression($tokens, $position);

            if (! isset($tokens[$position]) || $tokens[$position] !== ')') {
                throw new InvalidArgumentException('Missing closing parenthesis.');
            }

            $position++;

            return $value;
        }

        if ($token === '-') {
            $position++;

            return -$this->parseFactor($tokens, $position);
        }

        if (is_numeric($token)) {
            $position++;

            return (float) $token;
        }

        throw new InvalidArgumentException('Invalid token in formula.');
    }
}
