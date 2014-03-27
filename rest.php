<?php

include_once 'vendor/autoload.php';

$app= new \crazylunch\app\App ;


$app->resourcesConfigure();

$app->routes();
$app->run();