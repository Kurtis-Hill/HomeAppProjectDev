<?php

namespace App\Repository\Logs;

use Elastica\Index;
use Elastica\Query;
use Symfony\Component\Serializer\SerializerInterface;

class LogsRepository
{
    private Index $index;

//    private SerializerInterface $serializer;

    public function __construct(Index $index, SerializerInterface $serializer)
    {
        $this->index = $index;
//        $this->serializer = $serializer;
    }

    public function search()
    {
        $query = new Query();

        return $this->index->search($query);
//        $response = $this->index->
    }
}
