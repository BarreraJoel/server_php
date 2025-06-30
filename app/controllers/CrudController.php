<?php

namespace App\Controllers;

require_once "app/database/PdoHandler.php";

use App\Database\PdoHandler;
use App\Enums\CrudEnum;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class CrudController
{
    private PdoHandler $pdoHandler;

    public function __construct()
    {
        $configDb = require_once "config/database.php";
        $this->pdoHandler = new PdoHandler($configDb);
    }

    public function get(Request $request, Response $response, $args)
    {
        if (!$this->pdoHandler->checkTable('users')) {
            $response->getBody()->write(json_encode([
                'message' => 'La tabla no existe',
                'code' => 200
            ]));
        }
        $items = $this->pdoHandler->select('users', [
            'columns' => ['name']
        ]);

        $response->getBody()->write(json_encode([
            'message' => $items,
            'code' => 200
        ]));

        return $response;
    }

    public function getOne(Request $request, Response $response, $args)
    {
        if (!$this->pdoHandler->checkTable('users')) {
            $response->getBody()->write(json_encode([
                'message' => 'Table does not exists',
                'code' => 500
            ]));
        }
        $items = $this->pdoHandler->selectOne('users', [
            'columns' => ['email'],
            'constraint' => ['name' => 'natalia'],
        ]);

        $response->getBody()->write(json_encode([
            'message' => $items,
            'code' => 200
        ]));

        return $response;
    }

    public function post(Request $request, Response $response, $args)
    {
        if (!$this->pdoHandler->checkTable('users')) {
            $this->pdoHandler->createTable('users', ['name' => 'VARCHAR(25)', 'email' => 'VARCHAR(30) NULL']);
        }

        if ($this->pdoHandler->insert(
            'users',
            ['name' => 'jose', 'email' => 'jose@mail.com'],
        )) {
            $response->getBody()->write(json_encode([
                'message' => 'ok post',
                'code' => 201
            ]));
            return $response;
        }

        $response->getBody()->write(json_encode([
            'message' => 'error',
            'code' => 500
        ]));
        return $response;
    }
    public function put(Request $request, Response $response, $args)
    {
        if (!$this->pdoHandler->checkTable('users')) {
            $response->withStatus(500);
            $response->getBody()->write(json_encode([
                'message' => 'error',
                'code' => 500
            ]));
            return $response;
        }

        if (!$this->pdoHandler->update(
            'users',
            [
                'data' => ['name' => 'natalia mod'],
                'constraint' => ['email' => 'natalia@mail.com', 'name' => 'natalia']
            ],
        )) {
            $response->withStatus(500);
            $response->getBody()->write(json_encode([
                'message' => 'error',
                'code' => 500
            ]));
            return $response;
        }

        $response->withStatus(200);
        $response->getBody()->write(json_encode([
            'message' => 'ok put',
            'code' => 200
        ]));
        return $response;
    }

    public function delete(Request $request, Response $response)
    {
        if (!$this->pdoHandler->checkTable('users')) {
            $response->withStatus(500);
            $response->getBody()->write(json_encode([
                'message' => 'table does not exists',
                'code' => 500
            ]));
            return $response;
        }

        if (!$this->pdoHandler->delete(
            'users',
            ['constraint' => ['email' => 'jose@mail.com']],
        )) {
            $response->withStatus(500);
            $response->getBody()->write(json_encode([
                'message' => 'error',
                'code' => 500
            ]));
            return $response;
        }

        $response->withStatus(204);
        $response->getBody()->write(json_encode([
            'message' => 'ok delete',
            'code' => 204
        ]));
        return $response;
    }
}
