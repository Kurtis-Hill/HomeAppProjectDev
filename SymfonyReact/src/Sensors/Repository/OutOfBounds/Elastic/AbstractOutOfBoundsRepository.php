<?php

namespace App\Sensors\Repository\OutOfBounds\Elastic;


use Elastica\Index;

class AbstractOutOfBoundsRepository
{
    protected Index $index;

    public function __construct(Index $index)
    {
        $this->index = $index;
    }
}
