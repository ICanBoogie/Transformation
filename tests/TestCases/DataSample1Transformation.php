<?php

namespace ICanBoogie\Transformation\TestCases;

class DataSample1Transformation
{
    public function __invoke(DataSample1 $data, callable $transformation)
    {
        return get_class($data);
    }
}
