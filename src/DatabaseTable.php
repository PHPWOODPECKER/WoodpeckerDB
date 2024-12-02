<?php

namespace DatabaseTable;

require_once("./Tool.php");
require_once("./execption.php");

class WDB {

private static $pdo;
private static $primaryKey;

    /**
     * Establishes a connection to the MySQL database.
     *
     * This function attempts to connect to the specified MySQL database using the given credentials.
     * If the connection fails, an error message is displayed.
     *
     * @param string $host The host of the database server (e.g., localhost).
     * @param string $dbname The name of the database to connect to.
     * @param string $username The username for database authentication.
     * @param string $password The password for database authentication.
     * @param string $primaryKey The primary key for the database (used internally).
     */
    public static function connection(string $host, string $dbname, string $username, string $password, string $primaryKey): void {
      self::$primaryKey = $primaryKey;
        try {
            self::$pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }
    
    /**
     * Closes the current database connection.
     *
     * This function disconnects from the database by setting the PDO instance to null.
     */
    public static function disconnection(): void {
        self::$pdo = null;
    }

    private static function prepareAndExecute(string $query, array $params = []): \PDOStatement {
      try{
        $stmt = self::$pdo->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        $stmt->execute();
        return $stmt;
      }catch (PDOException $e) {
            throw new WDBException(" prepareAndExecute error: " . $e->getMessage());
        }
    }

    /**
     * Selects specific fields from the table.
     * 
     * @param string $table The table name.
     * @param string $field The field to select.
     * @return array An associative array of selected fields.
     * @throws Exception If there is an error during the select operation.
     */
    public static function select(string $table, string $field): \Tool {
        $table = self::validateInput(['table' => $table])['table'];
        $field = self::validateInput(['field' => $field])['field'];
        $sql = "SELECT `$field` FROM `$table`";
        try {
            $stmt = self::prepareAndExecute($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new WDBException("Select function error: " . $e->getMessage());
        }
    }

    /**
     * Finds a single record by a specific field and value.
     * 
     * @param string $table The table name.
     * @param string $field The field to search by.
     * @param string $value The value to search for.
     * @return array The found record as an associative array.
     * @throws Exception If there is an error during the find operation.
     */
    public static function find(string $table, string $field, string $value): \Tool {
        $table = self::validateInput(['table' => $table])['table'];
        $field = self::validateInput(['field' => $field])['field'];
        $value = self::validateInput(['value' => $value])['value'];
        $query = "SELECT * FROM `$table` WHERE `$field` = :value";
        
        try {
            $stmt = self::prepareAndExecute($query, [':value' => $value]);
            return new Tool(self::$pdo, $table, self::$primaryKey, $stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (PDOException $e) {
            throw new WDBException("Find function error: " . $e->getMessage());
        }
    }

/** * Finds records based on multiple conditions using AND or OR.
* 
* @param string $table The name of the table. 
* @param boolean that specifies whether the AND or OR find is true or false
* @param array $conditions An associative array of field-value pairs. 
* 
* return array @ an array of found records. 
* throws an @ exception if there is an error during the Find operation. 
*/
public static function findWith(string $table, bool $with, array $conditions): \Tool{
    $findtype = $with ? ' AND ' : ' OR ';
    $table = self::validateInput(['table' => $table])['table'];
    
    $where = [];
    $params = [];
    
    foreach ($conditions as $field => $value) {
        $field = self::validateInput(['field' => $field])['field'];
        $value = self::validateInput(['value' => $value])['value'];
        
        $where[] = "`$field` = :$field";
        $params["$field"] = $value;
    }
    
    if (count($where) === 0) {
        throw new WDBException("No conditions provided for WHERE clause.");
    }
    
    $query = "SELECT * FROM `$table` WHERE " . implode($findtype, $where);
    

    try {
        $stmt = self::prepareAndExecute($query, $params);
        return new Tool(self::$pdo, $table, self::$primaryKey, $stmt->fetchAll(PDO::FETCH_ASSOC));
    } catch (PDOException $e) {
        throw new WDBException("FindWith function error: " . $e->getMessage());
    }
}
    
    /**
     * Retrieves all records from the table.
     * 
     * @param string $table The table name.
     * @return array An array of all records.
     * @throws Exception If there is an error during the getAll operation.
     */
    public static function getAll(string $table): \Tool{
        $table = self::validateInput(['table' => $table])['table'];
        try {
            $stmt = self::prepareAndExecute("SELECT * FROM `$table`");
            
            return new Tool(self::$pdo, $table, self::$primaryKey, $stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (PDOException $e) {
            throw new WDBException("GetAll function error: " . $e->getMessage());
        }
    }

    /**
     * Retrieves the last record based on the primary key.
     * 
     * @param string $table The table name.
     * @return array The last record.
     * @throws Exception If there is an error during the getLast operation.
     */
    public static function getLast(string $table): \Tool {
        $table = self::validateInput(['table' => $table])['table'];
        $primaryKey1 = self::$primaryKey;
        $query = "SELECT * FROM `$table` ORDER BY `$primaryKey1` DESC LIMIT 1";
        try {
            $stmt = self::prepareAndExecute($query);
            
            return new Tool(self::$pdo, $table, self::$primaryKey, $stmt->fetch(PDO::FETCH_ASSOC));
        } catch (PDOException $e) {
            throw new WDBException("GetLast function error: " . $e->getMessage());
        }
    }

    /**
     * Retrieves the first record based on the primary key.
     * 
     * @param string $table The table name.
     * @return array The first record.
     * @throws Exception If there is an error during the getFirst operation.
     */
    public static function getFirst(string $table): \Tool {
        $table = self::validateInput(['table' => $table])['table'];
        $primaryKey1 = self::$primaryKey;
        $query = "SELECT * FROM `$table` ORDER BY `$primaryKey1` ASC LIMIT 1";
        try {
            $stmt = self::prepareAndExecute($query);
            
            return new Tool(self::$pdo, $table, self::$primaryKey, $stmt->fetch(PDO::FETCH_ASSOC));
        } catch (PDOException $e) {
            throw new WDBException("GetFirst function error: " . $e->getMessage());
        }
    }
  
    /**
     * Retrieves records from the specified table grouped by a specific field.
     * 
     * This function selects the distinct values from the specified `$field` and counts how many records match each value.
     * 
     * @param string $table The name of the table to query.
     * @param string $field The field to group by.
     * @return array An array of grouped records with their respective counts.
     * @throws Exception If there is an error during the query execution.
     */
    public static function getGrouped(string $table, string $field): \Tool {
        $table = self::validateInput(['table' => $table])['table'];
        $field = self::validateInput(['field' => $field])['field'];
      try{
        $query = "SELECT `$field`, COUNT(*) as count FROM `$table` GROUP BY `$field`";
        $stmt = self::prepareAndExecute($query);
        
        return new Tool(self::$pdo, $table, self::$primaryKey, $stmt->fetchAll(PDO::FETCH_ASSOC));
      }catch (PDOException $e) {
            throw new WDBException("GetGrouped function error: " . $e->getMessage());
        }
    }
    
    /**
     * Retrieves a random record from the specified table.
     * 
     * This function selects a random record from the given table. The result is based on random ordering.
     * 
     * @param string $table The name of the table to query.
     * @return array A random record from the table.
     * @throws Exception If there is an error during the query execution.
     */
    public static function getRandom(string $table): \Tool {
      $table = self::validateInput(['table' => $table])['table'];
      try{
        $query = "SELECT * FROM `$table` ORDER BY RAND() LIMIT 1";
        $stmt = self::prepareAndExecute($query);
        
        return new Tool(self::$pdo, $table, self::$primaryKey, $stmt->fetch(PDO::FETCH_ASSOC));
      }catch (PDOException $e) {
            throw new WDBException("GetRandom function error: " . $e->getMessage());
        }
    }


    /**
     * Returns the total number of records in the table.
     * 
     * @param string $table The table name.
     * @return int The total count of records.
     * @throws Exception If there is an error during the total operation.
     */
    public static function total(string $table): int {
        $table = self::validateInput(['table' => $table])['table'];
        try {
            $stmt = self::prepareAndExecute("SELECT COUNT(*) FROM `$table`");
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            throw new WDBException("Total function error: " . $e->getMessage());
        }
    }

    /**
     * Returns the total number of records that match a specific field and value.
     * 
     * @param string $table The table name.
     * @param string $field The field to count by.
     * @param string $value The value to count for.
     * @return int The count of matching records.
     * @throws Exception If there is an error during the totalField operation.
     */
    public static function totalField(string $table, string $field, string $value): int {
        $table = self::validateInput(['table' => $table])['table'];
        $field = self::validateInput(['field' => $field])['field'];
        $value = self::validateInput(['value' => $value])['value'];
        $query = "SELECT COUNT(*) FROM `$table` WHERE `$field` = :value";
        
        try {
            $stmt = self::prepareAndExecute($query, [':value' => $value]);
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            throw new WDBException("TotalField function error: " . $e->getMessage());
        }
    }

    /**
     * Saves a record to the database, either inserting or updating based on the presence of the primary key.
     * 
     * @param string $table The table name.
     * @param array $record The record data to save.
     * @throws Exception If there is an error during the save operation.
     */
    public static function save(string $table, array $record): void {
        $table = self::validateInput(['table' => $table])['table'];
        $record = self::validateInput($record);

        try {
            if (!isset($record[self::$primaryKey])) {
                self::insert($table, $record);
            } else {
                self::update($table, $record);
            }
        } catch (PDOException $e) {
            throw new WDBException("Save function error: " . $e->getMessage());
        }
    }

    private static function update(string $table, array $values): void {
        $query = "UPDATE `$table` SET ";
        $query .= implode(', ', array_map(fn($key) => "`$key` = :$key", array_keys($values)));
        $primaryKey1 = self::$primaryKey;
        $query .= " WHERE `$primaryKey1` = :primaryKey";

        $values['primaryKey'] = $values[self::$primaryKey];

        try {
            $stmt = self::prepareAndExecute($query, $values);
        } catch (PDOException $e) {
            throw new WDBException("Update function error: " . $e->getMessage());
        }
    }

    private static function insert(string $table, array $values): void {
        unset($values[self::$primaryKey]);
        $query = "INSERT INTO `$table` (" . implode(', ', array_map(fn($key) => "`$key`", array_keys($values))) . ")";
        $query .= " VALUES (" . implode(', ', array_map(fn($key) => ":$key", array_keys($values))) . ")";

        try {
            $stmt = self::prepareAndExecute($query, $values);
        } catch (PDOException $e) {
            throw new WDBException("Insert function error: " . $e->getMessage());
        }
    }
    
  /**
   * Updates a specific field in all records of the specified table.
   * 
   * This function sets the provided value for the specified field in every record of the table.
   * 
   * @param string $table The name of the table to update.
   * @param string $field The field to update.
   * @param mixed $value The value to set for the specified field.
   * @return void
   * @throws Exception If there is an error during the update operation.
   */
    public static function updateField(string $table, string $field, string $value): void {
      $table = self::validateInput(['table' => $table])['table'];
      $field = self::validateInput(['field' => $field])['field'];
      $value = self::validateInput(['value' => $value])['value'];
      try{
        $query = "UPDATE `$table` SET `$field` = :value";
        self::prepareAndExecute($query, [':value' => $value]);
      }catch (PDOException $e) {
            throw new WDBException("UpdateField function error: " . $e->getMessage());
        }
    }

    /**
     * Deletes a record from the table based on a specific field and value.
     * 
     * @param string $table The table name.
     * @param string $field The field to delete by.
     * @p aram string $value The value to delete for.
     * @throws Exception If there is an error during the delete operation.
     */
    public static function delete(string $table, string $field, string $value): void {
        $table = self::validateInput(['table' => $table])['table'];
        $field = self::validateInput(['field' => $field])['field'];
        $value = self::validateInput(['value' => $value])['value'];
        $query = "DELETE FROM `$table` WHERE `$field` = :value";
        
        try {
            self::prepareAndExecute($query, [':value' => $value]);
        } catch (PDOException $e) {
            throw new WDBException("Delete function error: " . $e->getMessage());
        }
    }
    
    /**
     * Truncates the specified table, removing all records.
     * 
     * This function removes all records from the specified table, but does not remove the table itself.
     * 
     * @param string $table The name of the table to truncate.
     * @return void
     * @throws Exception If there is an error during the truncate operation.
     */
    public static function truncate(string $table): void {
      $table = self::validateInput(['table' => $table])['table'];
      try{
        self::prepareAndExecute("TRUNCATE TABLE `$table`");
      }catch (PDOException $e) {
            throw new WDBException("Truncate function error: " . $e->getMessage());
        }
    }

    /**
     * Validates input data to ensure it is safe for database queries.
     * 
     * @param array $input The input data to validate.
     * @return array The validated input data.
     */
    public static function validateInput(array $input): array {
        $validated = [];
        foreach ($input as $key => $value) {
            if (is_string($value)) {
                $validated[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            } elseif (is_int($value)) {
                $validated[$key] = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
            } elseif (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $validated[$key] = filter_var($value, FILTER_SANITIZE_EMAIL);
            } elseif (filter_var($value, FILTER_VALIDATE_URL)) {
                $validated[$key] = filter_var($value, FILTER_SANITIZE_URL);
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
    public static function transaction(callable $callback): void {
        try {
            self::$pdo->beginTransaction();
            $callback(self::class);
            self::$pdo->commit();
        } catch (Exception $e) {
            self::$pdo->rollBack();
            throw new WDBException("Transaction error: " . $e->getMessage());
        }
    }
}

?>