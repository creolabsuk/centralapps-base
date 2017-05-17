<?php

$namespaces = array(
    'CentralApps\\Base' => __DIR__ .'/',
);

$loader = new \Symfony\Component\ClassLoader\UniversalClassLoader();
$loader->register();
$loader->registerNamespaces($namespaces);
