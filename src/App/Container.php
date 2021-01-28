<?php

namespace LoganStellway\Base\App;

use Reflection;

class Container
{
    /**
     * @var array
     */
    private $contents = [];

    /**
     * Create a new instance of a class
     */
    public function make($class)
    {
        if (!class_exists($class)) {
            throw new \Exception("Class '${class}' not found.");
        }

        $reflection = new Reflection($class);
    }

    /**
     * Get an instance of a class
     */
    public function get(string $class)
    {
        if (!isset($this->contents[$class])) {
            $this->contents[$class] = $this->make($class);
        }

        return $this->contents[$class];
    }
}
