<?php
/**
 * PYRAMEDIA - Database Configuration
 *
 * Now uses environment variables from .env file for security.
 * Credentials are no longer hardcoded.
 *
 * @package PYRAMEDIA
 * @since 1.0.0
 */

// Load environment configuration
if (file_exists(__DIR__ . '/bootstrap.php')) {
    require_once __DIR__ . '/bootstrap.php';
}

class Database {
    // Database credentials loaded from environment variables
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $charset = 'utf8mb4';

    public $conn;

    /**
     * Constructor - Load database credentials from environment
     */
    public function __construct() {
        // Load credentials from environment variables
        $this->host = env('DB_HOST', 'localhost');
        $this->db_name = env('DB_NAME', '');
        $this->username = env('DB_USER', '');
        $this->password = env('DB_PASS', '');

        // Validate that required credentials are present
        if (empty($this->db_name) || empty($this->username)) {
            throw new Exception('Database credentials not configured. Please check your .env file.');
        }
    }

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
            // Log the actual error for developers
            error_log("Database Connection Error: " . $exception->getMessage());

            // Show generic error to users (don't expose details)
            if (env('APP_DEBUG', false)) {
                echo "Connection error: " . $exception->getMessage();
            } else {
                echo "Database connection failed. Please contact support.";
            }
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
