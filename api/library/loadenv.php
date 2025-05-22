<?php
function loadJsonEnv($path)
{
  if (!file_exists($path)) {
    throw new Exception("Environment file not found at: $path");
  }

  $json = file_get_contents($path);
  $data = json_decode($json, true);

  if (!is_array($data)) {
    throw new Exception("Invalid JSON in environment file.");
  }

  foreach ($data as $key => $value) {
    $_ENV[$key] = $value;
  }
}
