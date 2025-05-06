<?php

namespace School\Adapters;

use Illuminate\Http\Resources\Json\JsonResource;
use \Illuminate\Support\Collection;
use School\Adapters\ConnecterInterface;

class Connecter
{
    protected $namespace = 'School\Adapters';
    protected $connecterClassName;


    /**
     * Undocumented function
     *
     * @param string $namespace
     * @param string $className
     * @return ConnecterInterface $connecter
     */
    public static function make(string $namespace, string $className = "Connecter")
    {
        $connecter = "\\School\\Adapters\\" . $namespace . "\\" . $className;

        return new $connecter();
    }
}
