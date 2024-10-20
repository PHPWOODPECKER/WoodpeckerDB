<?php

// Developer and programmer: Woodpeacker
// Version 2.0.0

class DatabaseTable {

    public function __construct(private \PDO $pdo, private string $table, private string $primaryKey) {
    }

    /**
     * Selects specific fields from the table.
     * 
     * @param string $field The field to select.
     * @return array An associative array of selected fields.
     * @throws Exception If there is an error during the select operation.
     */
    public function select(string $field): array {
        $field = $this->validateInput(['field' => $field])['field'];
        $sql = "SELECT `$field` FROM `$this->table`";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Select function error: " . $e->getMessage());
        }
    }

    /**
     * Finds a single record by a specific field and value.
     * 
     * @param string $field The field to search by.
     * @param string $value The value to search for.
     * @return array The found record as an associative array.
     * @throws Exception If there is an error during the find operation.
     */
    public function find(string $field, string $value): array {
        $field = $this->validateInput(['field' => $field])['field'];
        $value = $this->validateInput(['value' => $value])['value'];
        $query = "SELECT * FROM `$this->table` WHERE `$field` = :value";
        
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->bindValue(':value', $value, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Find function error: " . $e->getMessage());
        }
    }

    /**
     * Finds records based on multiple conditions using AND.
     * 
     * @param array $conditions An associative array of field-value pairs.
     * @return array An array of found records.
     * @throws Exception If there is an error during the find operation.
     */
    public function findWithAnd(array $conditions): array {
        $where = [];
        $params = [];
        foreach ($conditions as $field => $value) {
            $field = $this->validateInput(['field' => $field])['field'];
            $value = $this->validateInput(['value' => $value])['value'];
            $where[] = "`$field` = :$field";
            $params[$field] = $value;
        }
        $query = "SELECT * FROM `$this->table` WHERE " . implode(' AND ', $where);
        
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("FindWithAnd function error: " . $e->getMessage());
        }
    }
    
    /**
     * Finds records based on multiple conditions using OR.
     * 
     * @param array $conditions An associative array of field-value pairs.
     * @return array An array of found records.
     * @throws Exception If there is an error during the find operation.
     */
    public function findWithOr(array $conditions): array {
        $where = [];
        $params = [];
        foreach ($conditions as $field => $value) {
            $field = $this->validateInput(['field' => $field])['field'];
            $value = $this->validateInput(['value' => $value])['value'];
            $where[] = "`$field` = :$field";
            $params[$field] = $value;
        }
        $query = "SELECT * FROM `$this->table` WHERE " . implode(' OR ', $where);

        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("FindWithOr function error: " . $e->getMessage());
        }
    }

    /**
     * Retrieves all records from the table.
     * 
     * @return array An array of all records.
     * @throws Exception If there is an error during the getAll operation.
     */
    public function getAll(): array {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM `$this->table`");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("GetAll function error: " . $e->getMessage());
        }
    }
    
    /**
     * Retrieves the last record based on the primary key.
     * 
     * @return array The last record.
     * @throws Exception If there is an error during the getLast operation.
     */
    public function getLast(): array {
        $query = "SELECT * FROM `$this->table` ORDER BY `$this->primaryKey` DESC LIMIT 1";
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("GetLast function error: " . $e->getMessage());
        }
    }
    
    /**
     * Retrieves the first record based on the primary key.
     * 
     * @return array The first record.
     * @throws Exception If there is an error during the getFirst operation.
     */
    public function getFirst(): array {
        $query = "SELECT * FROM `$this->table` ORDER BY `$this->primaryKey` ASC LIMIT 1";
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("GetFirst function error: " . $e->getMessage());
        }
    }
  
    /**
     * Retrieves a paginated list of records.
     * 
     * @param int $limit The number of records to retrieve.
     * @param int $offset The offset for the records.
     * @return array An array of records.
     * @throws Exception If there is an error during the getPaginated operation.
     */
    public function getPaginated(int $limit, int $offset): array {
        $query = "SELECT * FROM `$this->table` LIMIT :limit OFFSET :offset";
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("GetPaginated function error: " . $e->getMessage());
        }
    }

    /**
     * Returns the total number of records in the table.
     * 
     * @return int The total count of records.
     * @throws Exception If there is an error during the total operation.
     */
    public function total(): int {
        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM `$this->table`");
            $stmt->execute();
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            throw new Exception("Total function error: " . $e->getMessage());
        }
    }

    /**
     * Returns the total number of records that match a specific field and value.
     * 
     * @param string $field The field to count by.
     * @param string $value The value to count for.
     * @return int The count of matching records.
     * @throws Exception If there is an error during the totalField operation.
     */
    public function totalField(string $field, string $value): int {
        $field = $this->validateInput(['field' => $field])['field'];
        $value = $this->validateInput(['value' => $value])['value'];
        $query = "SELECT COUNT(*) FROM `$this->table` WHERE `$field` = :value";
        
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->bindValue(':value', $value, PDO::PARAM_STR);
            $stmt->execute();
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            throw new Exception("TotalField function error: " . $e->getMessage());
        }
    }

    /**
     * Saves a record to the database, either inserting or updating based on the presence of the primary key.
     * 
     * @param array $record The record data to save.
     * @throws Exception If there is an error during the save operation.
     */
    public function save(array $record): void {
        $record = $this->sanitizeInput($record);
        $record = $this->validateInput($record);

        try {
            if (empty($record[$this->primaryKey])) {
                $this->insert($record);
            } else {
                $this->update($record);
            }
        } catch (PDOException $e) {
            throw new Exception("Save function error: " . $e->getMessage());
        }
    }

    private function update(array $values): void {
        $query = "UPDATE `$this->table` SET ";
        $query .= implode(', ', array_map(fn($key) => "`$key` = :$key", array_keys($values)));
        $query .= " WHERE `$this->primaryKey` = :primaryKey";

        $values['primaryKey'] = $values[$this->primaryKey];

        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($values);
        } catch (PDOException $e) {
            throw new Exception("Update function error: " . $e->getMessage());
        }
    }

    private function insert(array $values): void {
        unset($values[$this->primaryKey]);
        $query = "INSERT INTO `$this->table` (" . implode(', ', array_map(fn($key) => "`$key`", array_keys($values))) . ")";
        $query .= " VALUES (" . implode(', ', array_map(fn($key) => ":$key", array_keys($values))) . ")";

        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($values);
        } catch (PDOException $e) {
            throw new Exception("Insert function error: " . $e->getMessage());
        }
    }

    /**
     * Deletes a record from the table based on a specific field and value.
     * 
     * @param string $field The field to delete by.
     * @param string $value The value to delete for.
     * @throws Exception If there is an error during the delete operation.
     */
    public function delete(string $field, string $value): void {
        $field = $this->validateInput(['field' => $field])['field'];
        $value = $this->validateInput(['value' => $value])['value'];
        $query = "DELETE FROM `$this->table` WHERE `$field` = :value";
        
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->bindValue(':value', $value, PDO::PARAM_STR);
            $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Delete function error: " . $e->getMessage());
        }
    }

    /**
     * Sanitizes input data to prevent XSS attacks.
     * 
     * @param array $input The input data to sanitize.
     * @return array The sanitized input data.
     */
    public function sanitizeInput(array $input): array {
        foreach ($input as $key => $value) {
            if (is_string($value)) {
                $input[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            }
        }
        return $input;
    }

    /**
     * Validates input data to ensure it is safe for database queries.
     * 
     * @param array $input The input data to validate.
     * @return array The validated input data.
     */
    public function validateInput(array $input): array {
        $validated = [];
        foreach ($input as $key => $value) {
            if (is_string($value)) {
                $validated[$key] = filter_var($value, FILTER_SANITIZE_STRING);
            } elseif (is_int($value)) {
                $validated[$key] = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
            } elseif (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $validated[$key] = filter_var($value, FILTER_SANITIZE_EMAIL);
            } elseif (filter_var($value, FILTER_VALIDATE_URL)) {
                $validated[$key] = filter_var($value, FILTER_SANITIZE_URL);
            } else {
                $validated[$key] = $this->pdo->quote($value);
            }
        }
        return $validated;
    }

    /**
     * Manages database transactions.
     * 
     * @param callable $callback A callable that contains the operations to perform.
     * @throws Exception If there is an error during the transaction.
     */
    public function transaction(callable $callback): void {
        try {
            $this->pdo->beginTransaction();
            $callback($this);
            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw new Exception("Transaction error: " . $e->getMessage());
        }
    }
}
?>
