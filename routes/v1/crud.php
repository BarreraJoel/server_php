<?php

require_once "app/controllers/CrudController.php";

use Slim\Factory\AppFactory;
use App\Controllers\CrudController;

$app = AppFactory::create();

$crudController = new CrudController();

$app->get('/get', [$crudController, 'get']);
$app->get('/get_one', [$crudController, 'getOne']);
$app->post('/post', [$crudController, 'post']);
$app->put('/put', [$crudController, 'put']);
$app->delete('/delete', [$crudController, 'delete']);


$app->run();
