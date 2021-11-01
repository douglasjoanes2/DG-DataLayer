<?php

use DgCrud\DgCrud\DatabaseFactory;

require './vendor/autoload.php';

$conn = new DatabaseFactory();
$conn->connect();