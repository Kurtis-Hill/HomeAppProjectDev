<?php

namespace App\Repository\Common\Elastic;

use Elastica\Index;
use Symfony\Component\Serializer\SerializerInterface;

class LogRepository
{
    protected Index $index;

    protected SerializerInterface $serializer;

    public function __construct(Index $index, SerializerInterface $serializer)
    {
        $this->index = $index;
        $this->serializer = $serializer;
    }
}
