<?php

class Database
{
  private static $connection = null;

  private function __construct() {}

  public static function getConnection()
  {
    if (self::$connection === null) {
      $config = self::loadConfig();

      if (
        empty($config['DB_SERVER']) ||
        empty($config['DB_NAME']) ||
        empty($config['DB_USER']) ||
        !array_key_exists('DB_PASSWORD', $config)
      ) {
        die("Missing required DB config in env.json");
      }

      $host = $config['DB_SERVER'];
      $db   = $config['DB_NAME'];
      $user = $config['DB_USER'];
      $pass = $config['DB_PASSWORD'];
      $charset = $config['DB_CHARSET'] ?? 'utf8mb4';

      $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

      try {
        self::$connection = new PDO($dsn, $user, $pass, [
          PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
          PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
          PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
      } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
      }
    }

    return self::$connection;
  }

  private static function loadConfig(): array
  {
    $path = __DIR__ . '/../../env.json';

    if (!file_exists($path)) {
      die("env.json file not found at: $path");
    }

    $json = file_get_contents($path);
    $config = json_decode($json, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
      die("Invalid JSON in env.json: " . json_last_error_msg());
    }

    return $config;
  }
}
