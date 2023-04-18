<?php

function my_autoloader($class)
{
    $class_file = str_replace('\\', '/', $class) . '.php';

    if (file_exists($class_file)) {
        include_once $class_file;
    } else {
        echo "Class $class not found";
    }
}

spl_autoload_register('my_autoloader');
