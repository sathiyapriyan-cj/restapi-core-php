<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load env.json from project root
require_once __DIR__ . '/library/Database.class.php';
require_once __DIR__ . '/library/Signup.class.php';
require_once("REST.api.php");

class API extends REST
{
    private $db = NULL;

    public function __construct()
    {
        parent::__construct();
        $this->db = Database::getConnection();
    }


    // Public method for access API
    public function processApi()
    {
        $uri = $_SERVER['REQUEST_URI'];         // Full request URI
        $scriptName = $_SERVER['SCRIPT_NAME']; // Path to index.php

        // Remove query string (if any)
        $uri = strtok($uri, '?');

        // Remove the script's base directory from the URI
        $path = str_replace(dirname($scriptName), '', $uri);

        // Trim leading/trailing slashes
        $path = trim($path, '/');

        // Explode into segments
        $segments = explode('/', $path);

        // Check if the first segment exists (e.g., "test" in /api/test)
        $func = strtolower($segments[0] ?? '');

        if (method_exists($this, $func)) {
            $this->$func(); // Call method like $this->test()
        } else {
            $this->response(json_encode(['error' => 'Invalid Endpoint']), 404);
        }
    }
    /************* API METHODS START *******************/

    private function about()
    {
        if ($this->get_request_method() != "POST") {
            $error = $this->json([
                'status' => 'WRONG_CALL',
                "msg" => "The type of call cannot be accepted by our servers."
            ]);
            $this->response($error, 406);
        }

        $data = $this->json([
            'version' => $this->_request['version'] ?? 'unknown',
            'desc' => 'This API is created by Blovia Technologies Pvt. Ltd. for public use to access vehicle data.'
        ]);
        $this->response($data, 200);
    }

    private function verify()
    {
        if (
            $this->get_request_method() === "POST" &&
            isset($this->_request['user']) &&
            isset($this->_request['pass'])
        ) {
            $user = $this->_request['user'];
            $password = $this->_request['pass'];

            if ($user === "admin" && $password === "adminpass123") {
                $data = $this->json(["status" => "verified"]);
                $this->response($data, 200);
            } else {
                $data = $this->json(["status" => "unauthorized"]);
                $this->response($data, 401);
            }
        } else {
            $data = $this->json(["status" => "bad_request"]);
            $this->response($data, 400);
        }
    }

    private function test()
    {
        $headers = getallheaders();
        $data = $this->json($headers);
        $this->response($data, 200);
    }

    private function request_info()
    {
        $data = $this->json($_SERVER);
        $this->response($data, 200);
    }

    private function generate_hash()
    {
        $bytes = random_bytes(16);
        return bin2hex($bytes);
    }

    private function gen_hash()
    {
        if (isset($this->_request['pass'])) {
            $signup = new Signup("", $this->_request['pass'], "");

            $hash = $signup->hassPassword();
            $data = [
                'hash' => $hash,
                'val' => $this->_request['pass'],
                'verify' => password_verify($this->_request['pass'], $hash)
            ];

            $data = $this->json($data);
            $this->response($data, 200);
        }
    }

    /************* API METHODS END *********************/

    // Encode array into JSON
    private function json($data)
    {
        return is_array($data) ? json_encode($data, JSON_PRETTY_PRINT) : "{}";
    }
}

// Start API handling
$api = new API();
$api->processApi();
