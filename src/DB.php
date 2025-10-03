<?php

namespace gabogalro\SQLHelpers;

use PDO;
use PDOException;

class DB
{
    /**
     * Summary of instance
     * This is a singleton instance of the PDO connection. It is used to ensure that only one connection is made to the database.
     * @var PDO
     * @throws PDOException if the connection fails
     */
    private static $instance = null;


    /**
     * Returns a singleton instance of the PDO connection.
     *
     * @return PDO The PDO instance.
     * @throws PDOException if the connection fails.
     */
    public static function DB()
    {
        if (self::$instance === null) {

            $driver = $_ENV['DB_DRIVER'] ?? 'mysql';
            $database = $_ENV['DB_DATABASE'] ?? '';
            $user = $_ENV['DB_USERNAME'] ?? '';
            $pass = $_ENV['DB_PASSWORD'] ?? '';

            try {
                if (strtolower($driver) === 'mysql') {
                    $host = $_ENV['DB_SERVER'] ?? '127.0.0.1';
                    $charset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';
                    $dsn = "$driver:host=$host;dbname=$database;charset=$charset";

                    self::$instance = new PDO($dsn, $user, $pass);
                    self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                } elseif (strtolower($driver) === 'sqlsrv') {
                    $server = $_ENV['DB_SERVER'] ?? 'localhost';
                    $server = str_replace('\\', '\\\\', $server);
                    $dsn = "$driver:Server=$server;Database=$database";

                    self::$instance = new PDO($dsn, $user, $pass);
                    self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

                    if (defined('PDO::SQLSRV_ATTR_FETCHES_NUMERIC_TYPE')) {
                        self::$instance->setAttribute(PDO::SQLSRV_ATTR_FETCHES_NUMERIC_TYPE, true);
                    }
                } else {
                    throw new PDOException("Driver no soportado: $driver");
                }
            } catch (PDOException $e) {
                throw new PDOException('Connection failed: ' . $e->getMessage());
            }
        }
        return self::$instance;
    }
    /**
     * Executes a prepared statement with the given parameters.
     *
     * @param string $statement The SQL statement to execute.
     * @param array $params The parameters to bind to the statement.
     * @return bool The result set of the query. If the query does not return a result set, returns null.
     * @throws PDOException if the statement preparation or execution fails.
     */

    public static function statement($statement, $params = [])
    {
        try {
            $stmt = self::DB()->prepare($statement);
            $params = array_values($params);
            $stmt->execute($params);
            return true;
        } catch (PDOException $e) {
            throw new PDOException('Error on statement: ' . $e->getMessage());
        }
    }

    /**
     * Executes a select query and returns a single row.
     *
     * @param string $statement The SQL statement to execute.
     * @param mixed $id The parameter to bind to the statement.
     * @return array The fetched row as an associative array.
     * @throws PDOException if the query fails.
     */
    public static function selectOne($statement, $id)
    {
        try {
            $stmt = self::DB()->prepare($statement);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new PDOException('Error on select one: ' . $e->getMessage());
        }
    }

    /**
     * Executes a select query and returns all rows.
     *
     * @param string $statement The SQL statement to execute.
     * @param array $params The parameters to bind to the statement.
     * @return array The fetched rows as an associative array.
     * @throws PDOException if the query fails.
     * 
     */
    public static function selectAll($statement, $params = [])
    {
        try {
            $stmt = self::DB()->prepare($statement);
            $params = array_values($params);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new PDOException('Error on select all: ' . $e->getMessage());
        }
    }
    /**
     * Executes a select query and returns all rows.
     *
     * @param string $statement The SQL statement to execute.
     * @return array The fetched rows as an associative array.
     * @throws PDOException if the query fails.
     */
    public static function query($statement)
    {
        try {
            $stmt = self::DB()->prepare($statement);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new PDOException('Error on select: ' . $e->getMessage());
        }
    }
}
