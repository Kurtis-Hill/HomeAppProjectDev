<?php

namespace App\Repository\Common\Elastic;

use Elastica\Index;
use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\Match as Matching;
use Elastica\Query\MatchAll;
use Elastica\Query\Range;
use Elastica\Query\Term;
use Elastica\ResultSet;
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

    public function searchLogs(
        ?string $keyword = null,
        ?string $level = null,
        ?string $startDate = null,
        ?string $endDate = null,
        int $limit = 50,
        int $offset = 0,
    ): ResultSet {
        $bool = new BoolQuery();

        // Keyword full-text search on message
        if (!empty($keyword)) {
            $matchQuery = new Matching();
            $matchQuery->setFieldQuery('message', $keyword);
            $bool->addMust($matchQuery);
        } else {
            $bool->addMust(new MatchAll());
        }

        // Level filter
        if (!empty($level)) {
            $termQuery = new Term();
            $termQuery->setTerm('level', strtoupper($level));
            $bool->addFilter($termQuery);
        }

        // Date range filter on @timestamp
        if (!empty($startDate) || !empty($endDate)) {
            $rangeParams = [];
            if (!empty($startDate)) {
                $rangeParams['gte'] = $startDate;
            }
            if (!empty($endDate)) {
                $rangeParams['lte'] = $endDate;
            }
            $rangeQuery = new Range('@timestamp', $rangeParams);
            $bool->addFilter($rangeQuery);
        }

        $query = new Query($bool);
        $query->setSize($limit);
        $query->setFrom($offset);
        $query->setSort([['@timestamp' => ['order' => 'desc', 'unmapped_type' => 'date']]]);

        return $this->index->search($query);
    }
}
