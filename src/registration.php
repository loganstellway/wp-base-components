<?php

use LoganStellway\Base\App;

function lstellway_container()
{
    static $container;
    if (!$container) {
        $container = new App\Container();
    }
    return $container;
}
