<?php
declare(strict_types=1);

namespace Core\Validation;

use Core\Database\Connection;
use Core\I18n\Jalali;
use Core\I18n\Translator;

final class Validator
{
    public function __construct(
        private readonly Translator $translator,
        private readonly ?Connection $db = null,
        private readonly ?string $moduleScope = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, string|array<int, string|callable>> $rules
     * @return array<string, array<int, string>> Errors keyed by field
     */
    public function validate(array $data, array $rules, int $companyId = 0): array
    {
        $errors = [];

        foreach ($rules as $field => $fieldRules) {
            $ruleList = is_string($fieldRules) ? explode('|', $fieldRules) : $fieldRules;
            $value = $data[$field] ?? null;
            $isPresent = $value !== null && $value !== '';

            foreach ($ruleList as $rule) {
                if (is_callable($rule)) {
                    $res = $rule($value, $data);
                    if ($res !== true) {
                        $errors[$field][] = is_string($res)
                            ? $res
                            : $this->translator->trans('validation.custom', ['field' => $field]);
                    }
                    continue;
                }

                [$name, $param] = array_pad(explode(':', (string) $rule, 2), 2, null);

                if ($name === 'required') {
                    if (!$isPresent) {
                        $errors[$field][] = $this->translator->trans('validation.required', ['field' => $field]);
                    }
                    continue;
                }

                if (!$isPresent) {
                    continue;
                }

                $ok = match ($name) {
                    'string' => is_string($value),
                    'int' => filter_var($value, FILTER_VALIDATE_INT) !== false,
                    'decimal' => is_scalar($value) && preg_match('/^-?\d+(?:\.\d+)?$/', (string) $value) === 1,
                    'date' => is_string($value) && strtotime($value) !== false,
                    'jdate' => $this->checkJdate((string) $value),
                    'enum' => in_array((string) $value, explode(',', (string) $param), true),
                    'regex' => is_string($value) && preg_match((string) $param, $value) === 1,
                    'min' => is_numeric($value) ? (float) $value >= (float) $param : mb_strlen((string) $value) >= (int) $param,
                    'max' => is_numeric($value) ? (float) $value <= (float) $param : mb_strlen((string) $value) <= (int) $param,
                    'exists' => $this->checkExists((string) $param, $value, $companyId),
                    'unique' => $this->checkUnique((string) $param, $value, $companyId),
                    default => true,
                };

                if (!$ok) {
                    $errors[$field][] = $this->translator->trans("validation.$name", [
                        'field' => $field,
                        'param' => (string) $param,
                    ]);
                }
            }
        }

        return $errors;
    }

    private function checkJdate(string $value): bool
    {
        if (!preg_match('/^(\d{4})[\/\-](\d{1,2})[\/\-](\d{1,2})$/', $value, $m)) {
            return false;
        }
        return Jalali::isValidJalali((int) $m[1], (int) $m[2], (int) $m[3]);
    }

    private function checkExists(string $param, mixed $value, int $companyId): bool
    {
        if ($this->db === null) {
            return true;
        }
        [$table, $col] = array_pad(explode(',', $param, 2), 2, 'id');
        if ($this->moduleScope !== null && !str_starts_with($table, $this->moduleScope . '__')) {
            return false;
        }
        $row = $this->db->fetchOne("SELECT 1 FROM $table WHERE $col = ? AND company_id = ? LIMIT 1", [$value, $companyId]);
        return $row !== null;
    }

    private function checkUnique(string $param, mixed $value, int $companyId): bool
    {
        if ($this->db === null) {
            return true;
        }
        [$table, $col] = array_pad(explode(',', $param, 2), 2, 'id');
        $row = $this->db->fetchOne("SELECT 1 FROM $table WHERE $col = ? AND company_id = ? LIMIT 1", [$value, $companyId]);
        return $row === null;
    }
}
