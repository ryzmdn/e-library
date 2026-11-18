<?php
declare(strict_types=1);

namespace App\Core;

final class Validator
{
    private array $data;
    private array $rules;
    private array $errors = [];

    public function __construct(array $data, array $rules)
    {
        $this->data = $data;
        $this->rules = $rules;
    }

    public static function make(array $data, array $rules): self
    {
        $validator = new self($data, $rules);
        $validator->validate();
        return $validator;
    }

    public function validate(): bool
    {
        $this->errors = [];

        foreach ($this->rules as $field => $fieldRules) {
            if (is_string($fieldRules)) {
                $fieldRules = explode('|', $fieldRules);
            }

            $value = $this->data[$field] ?? null;

            foreach ($fieldRules as $rule) {
                $ruleName = $rule;
                $parameter = null;

                if (str_contains($rule, ':')) {
                    $parts = explode(':', $rule, 2);
                    $ruleName = $parts[0];
                    $parameter = $parts[1];
                }

                $this->applyRule($field, $value, $ruleName, $parameter);
            }
        }

        return empty($this->errors);
    }

    private function applyRule(string $field, mixed $value, string $ruleName, ?string $parameter): void
    {
        $stringValue = is_string($value) ? trim($value) : $value;

        switch ($ruleName) {
            case 'required':
                if ($value === null || $stringValue === '' || (is_array($value) && empty($value))) {
                    $this->addError($field, "Bidang {$field} wajib diisi.");
                }
                break;

            case 'email':
                if ($value !== null && $stringValue !== '' && !filter_var($stringValue, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, "Format email pada {$field} tidak valid.");
                }
                break;

            case 'min':
                $min = (int)$parameter;
                if (is_numeric($value)) {
                    if ((float)$value < $min) {
                        $this->addError($field, "Nilai {$field} minimal {$min}.");
                    }
                } elseif (is_string($value)) {
                    if (mb_strlen($stringValue) < $min) {
                        $this->addError($field, "Panjang {$field} minimal {$min} karakter.");
                    }
                }
                break;

            case 'max':
                $max = (int)$parameter;
                if (is_numeric($value)) {
                    if ((float)$value > $max) {
                        $this->addError($field, "Nilai {$field} maksimal {$max}.");
                    }
                } elseif (is_string($value)) {
                    if (mb_strlen($stringValue) > $max) {
                        $this->addError($field, "Panjang {$field} maksimal {$max} karakter.");
                    }
                }
                break;

            case 'numeric':
                if ($value !== null && $stringValue !== '' && !is_numeric($value)) {
                    $this->addError($field, "Bidang {$field} harus berupa angka.");
                }
                break;

            case 'alpha_num':
                if ($value !== null && $stringValue !== '' && !ctype_alnum(str_replace(['_', '-'], '', (string)$stringValue))) {
                    $this->addError($field, "Bidang {$field} hanya boleh berisi huruf dan angka.");
                }
                break;

            case 'matches':
                $otherField = (string)$parameter;
                $otherValue = $this->data[$otherField] ?? null;
                if ($value !== $otherValue) {
                    $this->addError($field, "Bidang {$field} tidak cocok dengan {$otherField}.");
                }
                break;

            case 'in':
                $allowed = explode(',', (string)$parameter);
                if ($value !== null && !in_array((string)$value, $allowed, true)) {
                    $this->addError($field, "Pilihan pada {$field} tidak valid.");
                }
                break;
        }
    }

    private function addError(string $field, string $message): void
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        $this->errors[$field][] = $message;
    }

    public function passes(): bool
    {
        return empty($this->errors);
    }

    public function fails(): bool
    {
        return !empty($this->errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getFirstError(?string $field = null): ?string
    {
        if ($field !== null) {
            return $this->errors[$field][0] ?? null;
        }

        foreach ($this->errors as $fieldErrors) {
            if (!empty($fieldErrors)) {
                return $fieldErrors[0];
            }
        }

        return null;
    }
}
