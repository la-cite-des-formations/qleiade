<?php

namespace School\Traits;

use Illuminate\Http\Resources\Json\JsonResource;
use School\Adapters\Mapper as AdaptMapper;
use \Illuminate\Support\Collection;

trait Mapper
{
    /**
     * Undocumented function
     *
     * @param string $adapter
     * @param string $resource
     * @param Collection $collection
     * @return [type] $resourceCollection
     */
    public function getData(string $adapter, string $resource, Collection $collection)
    {
        $mapper = new AdaptMapper($adapter);
        $resource = $mapper->get($resource, $collection);
        // $r = $resource::class;
        return $resource::collection($collection);
    }
}
