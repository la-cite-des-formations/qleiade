<?php

namespace School\Adapters;

interface ConnecterInterface
{
    public function get($uri = "", $tab = null, $filterResponse = false, $where = null, $params = [], $filters = [], $formationType = null);
    public function formatResponse($response, $filter = null);
}
