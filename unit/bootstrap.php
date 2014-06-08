<?php
require_once(__DIR__.'/../../vendor/autoload.php');
require_once(__DIR__.'/splClassLoader.php');
$classLoader = new SplClassLoader('CentralApps', __DIR__.'/../');
$classLoader->register();
