<?php

namespace App\Core;

/**
 * Validasi input sisi server (bukan hanya mengandalkan validasi HTML/JS).
 * Kumpulkan semua pesan error lalu kembalikan sekaligus.
 */
class Validator
{
    private array $errors = [];
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function required(string $field, string $label): self
    {
        $value = trim((string) ($this->data[$field] ?? ''));
        if ($value === '') {
            $this->errors[$field] = "{$label} wajib diisi.";
        }
        return $this;
    }

    public function email(string $field, string $label = 'Email'): self
    {
        $value = $this->data[$field] ?? '';
        if ($value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = "{$label} tidak valid.";
        }
        return $this;
    }

    public function numeric(string $field, string $label): self
    {
        $value = $this->data[$field] ?? '';
        if ($value !== '' && !is_numeric($value)) {
            $this->errors[$field] = "{$label} harus berupa angka.";
        }
        return $this;
    }

    public function min(string $field, int $length, string $label): self
    {
        $value = (string) ($this->data[$field] ?? '');
        if ($value !== '' && strlen($value) < $length) {
            $this->errors[$field] = "{$label} minimal {$length} karakter.";
        }
        return $this;
    }

    public function unique(string $field, string $label, string $table, ?int $exceptId = null): self
    {
        $value = $this->data[$field] ?? '';
        if ($value === '') {
            return $this;
        }
        $db = Database::connect();
        $sql = "SELECT COUNT(*) FROM {$table} WHERE {$field} = :value";
        $params = ['value' => $value];
        if ($exceptId) {
            $sql .= " AND id != :id";
            $params['id'] = $exceptId;
        }
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        if ((int) $stmt->fetchColumn() > 0) {
            $this->errors[$field] = "{$label} sudah digunakan.";
        }
        return $this;
    }

    public function fails(): bool
    {
        return !empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }
}
