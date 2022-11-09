<?php

// use Monolog\Handler\RotatingFileHandler;

use Demo\MonologMaxSize\RotatingAndSizeFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;

require_once "./vendor/autoload.php";

// 1024 -> 1 KB
// 1,048,576 -> 1 MB
// 1,073,741,824 -> 1GB

$config =  [
    'name' => 'helloStack',
    'path' => __DIR__ . '/mylog.log',
    'maxSize' => 10485760,   // 10 MB 左右
    'maxFiles' => 3
];

// print_r($config);exit;
$log = new Logger($config['name']);
$handler = new RotatingAndSizeFileHandler($config['path'], 5, $config['maxSize']);


// $log->pushHandler(new StreamHandler($config['path']));
$log->pushHandler($handler);

$count = 0;
while(1) {
    $log->error(++$count);
    $log->warning(++$count);
}



