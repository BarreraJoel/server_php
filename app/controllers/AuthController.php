<?php

namespace App\Controllers;

require_once "app/database/PdoHandler.php";

use App\Database\PdoHandler;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class AuthController
{
    private PdoHandler $pdoHandler;

    public function __construct()
    {
        $configDb = require_once "config/database.php";
        $this->pdoHandler = new PdoHandler($configDb);
    }

    public function login(Request $request, Response $response, $args)
    {
        $response->getBody()->write(json_encode([
            'message' => 'ok post',
            'code' => 200
        ]));

        return $response;
    }

    public function user(Request $request, Response $response)
    {
        $response->getBody()->write(json_encode([
            'message' => 'ok post',
            'code' => 200
        ]));

        return $response;
    }
}
