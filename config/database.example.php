<?php
/**
 * PYRAMEDIA - Database Configuration Template
 * Copy this file to database.php and update with your credentials
 */

class Database {
    // Database credentials - UPDATE THESE!
    private $host = 'localhost';
    private $db_name = 'pyramedia_db';
    private $username = 'your_username';  // CHANGE THIS
    private $password = 'your_password';  // CHANGE THIS
    private $charset = 'utf8mb4';

    public $conn;

    /**
     * Get database connection
     */
    public function getConnection() {
        $this->conn = null;

        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=" . $this->charset;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }

    /**
     * Execute query and return results
     */
    public function query($sql, $params = []) {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch(PDOException $e) {
            error_log("Query error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get single row
     */
    public function single($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt ? $stmt->fetch() : false;
    }

    /**
     * Get all rows
     */
    public function all($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt ? $stmt->fetchAll() : false;
    }

    /**
     * Insert record and return last inserted ID
     */
    public function insert($table, $data) {
        $keys = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO {$table} ({$keys}) VALUES ({$placeholders})";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($data);
            return $this->conn->lastInsertId();
        } catch(PDOException $e) {
            error_log("Insert error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update record
     */
    public function update($table, $data, $where, $whereParams = []) {
        $set = [];
        foreach (array_keys($data) as $key) {
            $set[] = "{$key} = :{$key}";
        }
        $set = implode(', ', $set);

        $sql = "UPDATE {$table} SET {$set} WHERE {$where}";

        try {
            $stmt = $this->conn->prepare($sql);
            $params = array_merge($data, $whereParams);
            $stmt->execute($params);
            return $stmt->rowCount();
        } catch(PDOException $e) {
            error_log("Update error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete record
     */
    public function delete($table, $where, $params = []) {
        $sql = "DELETE FROM {$table} WHERE {$where}";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->rowCount();
        } catch(PDOException $e) {
            error_log("Delete error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Count records
     */
    public function count($table, $where = '', $params = []) {
        $sql = "SELECT COUNT(*) as count FROM {$table}";
        if ($where) {
            $sql .= " WHERE {$where}";
        }

        $result = $this->single($sql, $params);
        return $result ? (int)$result['count'] : 0;
    }

    /**
     * Begin transaction
     */
    public function beginTransaction() {
        return $this->conn->beginTransaction();
    }

    /**
     * Commit transaction
     */
    public function commit() {
        return $this->conn->commit();
    }

    /**
     * Rollback transaction
     */
    public function rollback() {
        return $this->conn->rollBack();
    }

    /**
     * Close connection
     */
    public function close() {
        $this->conn = null;
    }
}

/**
 * Helper function to get database instance
 */
function getDB() {
    static $db = null;
    if ($db === null) {
        $db = new Database();
        $db->getConnection();
    }
    return $db;
}
?>
