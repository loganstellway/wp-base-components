<?php

function lstellway_container()
{
    static $container;
    if (!$container) {
        $container = new \LoganStellway\Base\App\Container();
    }
    return $container;
}
