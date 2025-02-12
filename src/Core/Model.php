<?php
class Model
{
    public $table;
    protected $primaryKey = 'id';
    protected $pdo;
    protected $relations = '';
    protected $collection = [];


    public function __construct()
    {
        $config = require __DIR__ . '/../../config/database.php';
        $this->pdo = Database::getInstance($config['host'], $config['database'], $config['username'], $config['password'])->getPDO();
    }

    protected function mapResultToProperties($data)
    {
        if ($data) {
            foreach ($data as $key => $value) {
                $this->{$key} = $value;
            }
        }
    }

    public function with($relation)
    {
        $this->relations = $relation;
        return $this;
    }

    public function find($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $this->mapResultToProperties($result);
            return $result; // Return the populated object
        }
        return false;
    }

    public function where($operator, array $attributes)
    {
        $conditions = [];
        $params = [];
        foreach ($attributes as $key => $value) {
            $conditions[] = "{$key} {$operator} :{$key}";
            $params[":{$key}"] = $value;
        }
        $where = implode(' AND ', $conditions);
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE {$where}");
        $stmt->execute($params);
        if ($results = $stmt->fetchAll(PDO::FETCH_ASSOC)) {
            if ($relationship = $this->relations) {
                $this->collection = $results;
                $books = $this->$relationship();
                foreach ($results as &$result) {
                    $result['books'] = $books[$result['id']];
                }
            }
            return $results; // Return the populated object
        }
        return false;
    }

    public function whereIn($column, array $values)
    {
        // Generate placeholders for the values
        $placeholders = implode(', ', array_fill(0, count($values), '?'));

        // Prepare and execute the query
        $sql = "SELECT * FROM $this->table WHERE $column IN ($placeholders)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($values);

        // Fetch results
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return new static::$collection($results);
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
        $returning = array_merge(['id'], array_keys($data), ['created_at']);
        $stmt = $this->pdo->prepare("INSERT INTO {$this->table} ($columns) VALUES ($placeholders) RETURNING " . implode(', ', $returning));
        // Execute the query
        if ($stmt->execute($data)) {
            // Fetch the last inserted ID
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            // Retrieve and return the inserted record
            return $data;
        }
    }

    public function createOrUpdate(array $attributes, array $values = [])
    {
        $result = $this->where('=', $attributes);
        if (! $result) {
            return $this->create(array_merge($attributes, $values));
        }

        if (empty($values)) {
            return false;
        }
        $this->update($result[0]['id'], $values);
        return $this->find($result[0]['id']);
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
            $updates = [];
            foreach ($column as $value) {
                $values[] = $row[$value];
                $updates[] = "{$value} = EXCLUDED.{$value}";
            }
        }

        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES " . implode(", ", $placeholders) . " ON CONFLICT ({$field}) DO UPDATE SET " . implode(", ", $updates);
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

    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function hasMany($relatedClass, $foreignKey, $localKey = 'id')
    {
        $related = new $relatedClass();

        if (count($this->collection)) {
            $values = array_column($this->collection, 'id');
            $placeholders = implode(', ', array_fill(0, count($values), '?'));

            // Prepare and execute the query
            $sql = "SELECT * FROM {$related->table} WHERE {$foreignKey} IN ($placeholders)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($values);
            if ($result = $stmt->fetchAll(PDO::FETCH_ASSOC)); {
                $grouped = [];
                foreach ($result as $item) {
                    $grouped[$item[$foreignKey]][] = $item;
                }
                return $grouped;
            }
            return false;
        }

        $stmt = $this->pdo->prepare("SELECT * FROM {$related->table} WHERE {$foreignKey} = :value");
        $stmt->execute(['value' => $this->{$localKey}]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
