<?php
class Model
{
    protected $table;
    protected $primaryKey = 'id';
    protected $pdo;

    public function __construct()
    {
        $config = require __DIR__ . '/../../config/database.php';
        $this->pdo = Database::getInstance($config['host'], $config['database'], $config['username'], $config['password'])->getPDO();
    }

    public function find($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function where(array $attributes)
    {
        $conditions = [];
        foreach ($attributes as $key => $value) {
            $conditions[] = "{$key} = :{$key}";
        }
        $where = implode(' AND ', $conditions);
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE {$where}");
        $stmt->execute($attributes);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function all()
    {
        $stmt = $this->pdo->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $columns = implode(',', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $stmt = $this->pdo->prepare("INSERT INTO {$this->table} ($columns) VALUES ($placeholders)");
        return $stmt->execute($data);
    }

    public function insert(array $data)
    {
        $columns = implode(', ', array_keys(reset($data)));
        $values = [];

        foreach ($data as $row) {
            $placeholder = implode(', ', array_fill(0, count($row), '?'));
            $column = array_keys($row);
            $placeholders[] = "({$placeholder})";
            foreach ($column as $value) {
                $values[] = $row[$value];
            }
        }

        $sql = "INSERT INTO example_table ({$columns}) VALUES " . implode(", ", $placeholders);
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($values);
    }

    public function upsert($field, array $data)
    {
        $columns = implode(', ', array_keys(reset($data)));
        $values = [];

        foreach ($data as $row) {
            $placeholder = implode(', ', array_fill(0, count($row), '?'));
            $column = array_keys($row);
            $placeholders[] = "({$placeholder})";
            foreach ($column as $value) {
                $values[] = $row[$value];
            }
        }

        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES " . implode(", ", $placeholders) . " ON CONFLICT ({$field}) DO UPDATE SET value = EXCLUDED.value";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($values);
    }

    public function update($id, $data)
    {
        $fields = '';
        foreach ($data as $key => $value) {
            $fields .= "$key = :$key, ";
        }
        $fields = rtrim($fields, ', ');

        $data['id'] = $id;
        $stmt = $this->pdo->prepare("UPDATE {$this->table} SET $fields WHERE {$this->primaryKey} = :id");
        return $stmt->execute($data);
    }

    public function updateOrInsert(array $attributes, array $values = [])
    {
        $result = $this->where($attributes);
        if (! $result) {
            return $this->create(array_merge($attributes, $values));
        }

        if (empty($values)) {
            return true;
        }

        return (bool) $this->update($result->id, $values);
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id");
        return $stmt->execute(['id' => $id]);
    }
}
