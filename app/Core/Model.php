<?php

namespace App\Core;

use PDO;
use PDOException;

/**
 * Base Model: setiap model turunan wajib set $table.
 * Semua query menggunakan PDO Prepared Statement (anti SQL Injection).
 */
abstract class Model
{
    protected PDO $db;
    protected string $table;
    protected string $primaryKey = 'id';

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function all(string $orderBy = 'id DESC'): array
    {
        try {
            $stmt = $this->db->query("SELECT * FROM {$this->table} ORDER BY {$orderBy}");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    public function find(int $id): ?array
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1");
            $stmt->execute(['id' => $id]);
            $row = $stmt->fetch();
            return $row ?: null;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    public function findBy(string $column, $value): ?array
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$column} = :value LIMIT 1");
            $stmt->execute(['value' => $value]);
            $row = $stmt->fetch();
            return $row ?: null;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    public function where(string $column, $value, string $orderBy = 'id DESC'): array
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$column} = :value ORDER BY {$orderBy}");
            $stmt->execute(['value' => $value]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    public function insert(array $data): int|false
    {
        try {
            $columns = implode(', ', array_keys($data));
            $placeholders = ':' . implode(', :', array_keys($data));
            $stmt = $this->db->prepare("INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})");
            $stmt->execute($data);
            return (int) $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function update(int $id, array $data): bool
    {
        try {
            $set = implode(', ', array_map(fn($col) => "{$col} = :{$col}", array_keys($data)));
            $data[$this->primaryKey] = $id;
            $stmt = $this->db->prepare("UPDATE {$this->table} SET {$set} WHERE {$this->primaryKey} = :{$this->primaryKey}");
            return $stmt->execute($data);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function delete(int $id): bool
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id");
            return $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function count(): int
    {
        try {
            return (int) $this->db->query("SELECT COUNT(*) FROM {$this->table}")->fetchColumn();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return 0;
        }
    }
}
