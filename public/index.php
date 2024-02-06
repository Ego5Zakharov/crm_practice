<?php

use App\Kernel\App;

define("APP_PATH", dirname(__DIR__));

require_once APP_PATH . "/vendor/autoload.php";

$application = new App();

$application->run();

