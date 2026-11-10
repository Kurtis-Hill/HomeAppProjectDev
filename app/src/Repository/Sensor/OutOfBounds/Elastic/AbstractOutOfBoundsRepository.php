<?php

declare(strict_types=1);

namespace App\Repository\Sensor\OutOfBounds\Elastic;

use App\Builders\Sensor\Internal\ReadingType\ReadingTypeCreationBuilders\SensorOutOfBoundsCreationBuilders\Elastic\OutOfBoundsPersistenceDTOBuilder;
use App\DTOs\Sensor\Request\OutOfBounds\Elastic\OutOfBoundsElasticPersistenceDTO;
use App\Entity\Sensor\OutOfRangeRecordings\OutOfBoundsEntityInterface;
use DateTimeInterface;
use Elastica\Document;
use Elastica\Index;
use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\MatchAll;
use Elastica\Query\Range;
use Elastica\Query\Term;
use Symfony\Component\Serializer\SerializerInterface;

abstract class AbstractOutOfBoundsRepository implements OutOfBoundsElasticQueryInterface
{
    public const DIRECTION_ABOVE = 'above';
    public const DIRECTION_BELOW = 'below';

    protected Index $index;

    protected SerializerInterface $serializer;

    public function __construct(Index $index, SerializerInterface $serializer)
    {
        $this->index = $index;
        $this->serializer = $serializer;
    }

    public function getIndex(): Index
    {
        return $this->index;
    }

    protected function serializeOutOfBoundsEntity(OutOfBoundsElasticPersistenceDTO $outOfBoundsEntity): array
    {
        return $this->serializer->normalize($outOfBoundsEntity, 'json');
    }

    public function persist(OutOfBoundsEntityInterface $outOfBoundsEntity): void
    {
        $outOfBoundsUpdateDTO = OutOfBoundsPersistenceDTOBuilder::buildOutOfBoundsPersistenceDTO($outOfBoundsEntity);

        $serializedUpdateDTO = $this->serializeOutOfBoundsEntity($outOfBoundsUpdateDTO);
        $this->index->addDocument(
            new Document(null, $serializedUpdateDTO)
        );

        $this->index->refresh();
    }

    public function findByReadingsBeyondThreshold(float $threshold, string $direction, int $limit = 500, int $offset = 0): array
    {
        $rangeParams = $direction === self::DIRECTION_ABOVE
            ? ['gte' => $threshold]
            : ['lte' => $threshold];

        $boolQuery = new BoolQuery();
        $boolQuery->addMust(new Range('sensorReading', $rangeParams));

        $query = $this->buildBaseQuery($limit, $offset);
        $query->setQuery($boolQuery);

        return $this->search($query);
    }

    public function findByDateRange(DateTimeInterface $from, DateTimeInterface $to, int $limit = 500, int $offset = 0): array
    {
        $boolQuery = new BoolQuery();
        $boolQuery->addMust(new Range('createdAt', [
            'gte' => $from->format(DateTimeInterface::ATOM),
            'lte' => $to->format(DateTimeInterface::ATOM),
        ]));

        $query = $this->buildBaseQuery($limit, $offset);
        $query->setQuery($boolQuery);

        return $this->search($query);
    }

    public function findBySensorReadingId(int $sensorReadingId): array
    {
        $query = new Query(new Term(['sensorReadingID' => $sensorReadingId]));

        return $this->search($query);
    }

    public function findAllPaginated(int $limit = 500, int $offset = 0): array
    {
        $query = $this->buildBaseQuery($limit, $offset);
        $query->setQuery(new MatchAll());

        return $this->search($query);
    }

    public function search(Query $query): array
    {
        $resultSet = $this->index->search($query);

        return array_map(
            static fn($result) => $result->getData(),
            $resultSet->getResults()
        );
    }

    private function buildBaseQuery(int $limit, int $offset): Query
    {
        $query = new Query();
        $query->setSize($limit);
        $query->setFrom($offset);

        return $query;
    }
}
