<?php

class Tool {
    
  private string $table;
  private string $primaryKey;
  private array $collection;
  private \PDO $pdo;
  
  public function __construct(\PDO $pdo, string $table, string $primaryKey, array $collection){
    if(empty($collection)) throw new WDBException("collection is empty.");
    $this->table = $table;
    $this->primaryKey = $primaryKey;
    $this->collection = $collection;
    $this->pdo = $pdo;
  }


    /**
     * Returns the collection as an array.
     * 
     * @return array The data collection.
     * @throws Exception If collection is not an array.
     */
    public function getArray(): array {
        if (!is_array($this->collection)) {
            throw new WDBException("getArrayFunction : Collection is not a valid array.");
        }
        return $this->collection;
    }

    /**
     * Returns the collection as a JSON string.
     * 
     * @return string JSON encoded collection.
     * @throws Exception If JSON encoding fails.
     */
    public function getJson(): string {
        try {
            return json_encode($this->collection, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new WDBException("Error encoding JSON: " . $e->getMessage());
        }
    }

    /**
     * Filters the collection by a field and value.
     * 
     * @param string $field The field to filter by.
     * @param mixed $value The value to match.
     * @return array The filtered collection.
     */
    public function find(string $field, $value): array {
    $rows = [];
    foreach ($this->collection as $row){
      if(isset($row[$field]) && $row[$field] == $value){
        $rows[] = $row;
      }
    }
}



    /**
     * Deletes an item based on the primary key from the collection and database.
     * 
     * @throws Exception If the primary key is not found or the deletion fails.
     */
    public function delete(): void {
        if (!isset($this->collection[0][$this->primaryKey])) {
            throw new WDBException("Primary key '{$this->primaryKey}' not found in the collection.");
        }
        

        $value = $this->collection[0][$this->primaryKey];
        $query = "DELETE FROM `{$this->table}` WHERE `{$this->primaryKey}` = :value";

        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(":value", $value, \PDO::PARAM_STR);
            $stmt->execute();
        } catch (\PDOException $e) {
            throw new WDBException("deleteFunction : " . $e->getMessage());
        }
    }

    /**
     * Updates an item in the collection and the corresponding database record.
     * 
     * @param array $values The values to update.
     * @throws Exception If the primary key is missing or the update fails.
     */
    public function update(array $values): void {
        if (!isset($this->collection[0][$this->primaryKey])) {
            throw new WDBException("Primary key '{$this->primaryKey}' not found in the collection.");
        }
        

        $values[$this->primaryKey] = $this->collection[0][$this->primaryKey];

        $query = "UPDATE `{$this->table}` SET ";
        $query .= implode(', ', array_map(fn($key) => "`$key` = :$key", array_keys($values)));
        $query .= " WHERE `{$this->primaryKey}` = :{$this->primaryKey}";

        try {
            $stmt = $this->pdo->prepare($query);

            foreach ($values as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }

            $stmt->execute();
        } catch (\PDOException $e) {
            throw new WDBException("updateFunction : " . $e->getMessage());
        }
    }

    /**
     * Returns the count of items in the collection.
     * 
     * @return int The count of items in the collection.
     */
    public function count(): int {
        return count($this->collection);
    }

    /**
     * Returns the last item in the collection.
     * 
     * @return array The last item in the collection.
     * @throws Exception If the collection is empty.
     */
    public function getLast(): array {
        if(count($this->collection) <= 2){
            throw new WDBException("getLastFunction : Collection must have at least 2 values.");
        }
        return $this->collection[count($this->collection) - 1];
    }

    /**
     * Returns the first item in the collection.
     * 
     * @return array The first item in the collection.
     * @throws Exception If the collection is empty.
     */
    public function getFirst(): array {
        if(count($this->collection) <= 2){
          throw new WDBException("getFirstFunction : Collection must have at least 2 values.");
        }
        return $this->collection[0];
    }

    /**
     * Returns a random item from the collection.
     * 
     * @return array A random item from the collection.
     * @throws Exception If the collection is empty.
     */
    public function getRandom(): array {
        $randomKey = array_rand($this->collection);
        return $this->collection[$randomKey];
    }

    /**
     * Groups the collection by a specific key or set of keys.
     * 
     * @param string|array $key The key or keys to group by.
     * @return array The grouped collection.
     */
    public function groupBy($key): array {
        if(count($this->collection) <= 2){
          throw new WDBException("groupByFunction : Collection must have at least 2 values.");
        }
        $result = [];
        foreach ($this->collection as $item) {
            if (is_array($key)) {
                $group = implode('-', array_map(fn($k) => $item[$k] ?? '', $key));
            } else {
                $group = $item[$key] ?? '';
            }

            if (!isset($result[$group])) {
                $result[$group] = [];
            }
            $result[$group][] = $item;
        }
        return $result;
    }
}

?>
