<?php

use Dougl\DataLayer\DataLayer;

require './vendor/autoload.php';

define('DB_CONFIG', [
    'driver'    => 'mysql',
    'port'      => '3306',
    'host'      => 'localhost',
    'db_name'   => 'db_testes',
    'db_user'   => 'root',
    'db_passwd' => ''
]);

$db_layer = new DataLayer('user', ['name', 'email', 'passwd'], 'id', true);
$db_layer->id = 5;
//$db_layer->name = 'Davi';
//$db_layer->email = 'douglasjoanes2@hotmail.com';
//$db_layer->passwd = '123456';

var_dump($db_layer->destroy());
exit;