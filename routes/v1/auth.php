<?php

require "app/controllers/AuthController.php";

use Slim\Factory\AppFactory;
use App\Controllers\AuthController;

$app = AppFactory::create();

$authController = new AuthController();

$app->get('/user', [$authController, 'user']);
$app->post('/login', [$authController, 'login']);


$app->run();
