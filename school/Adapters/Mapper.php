<?php

namespace School\Adapters;

use Illuminate\Http\Resources\Json\JsonResource;
use \Illuminate\Support\Collection;

class Mapper
{
    protected $namespace = 'School\Adapters\Ypareo';

    public function __construct(string $namespace)
    {
        $this->namespace = "\\School\\Adapters\\" . $namespace;
    }

    /**
     * Undocumented function
     *
     * @param string $resourceName
     * @param Collection $items
     * @return JsonResource
     */
    public function get(string $resourceName, Collection $items)
    {
        $mapper = $this->namespace . "\\Resources\\" . $resourceName;

        return new $mapper($items);
    }
}
