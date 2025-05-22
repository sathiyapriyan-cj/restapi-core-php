<?php

class Database
{
  private static $db = null;

  /**
   * Get the database connection instance
   */
  public static function getConnection()
  {
    if (self::$db !== null) {
      return self::$db;
    }

    // Load environment variables (if not already)
    $server = $_ENV['DB_SERVER'] ?? '';
    $user = $_ENV['DB_USER'] ?? '';
    $pass = $_ENV['DB_PASSWORD'] ?? '';
    $dbname = $_ENV['DB_NAME'] ?? '';

    // Connect to database
    self::$db = mysqli_connect($server, $user, $pass, $dbname);

    if (!self::$db) {
      die("Database connection failed: " . mysqli_connect_error());
    }

    mysqli_set_charset(self::$db, 'utf8mb4');
    return self::$db;
  }
}
