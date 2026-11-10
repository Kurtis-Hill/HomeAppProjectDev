<?php

declare(strict_types=1);

namespace App\Services\Sensor\OutOfBounds\Elastic;

use App\DTOs\Sensor\Request\OutOfBounds\GetOutOfBoundsReadingsRequestDTO;
use App\DTOs\Sensor\Response\OutOfBounds\OutOfBoundsReadingResponseDTO;
use App\Repository\Sensor\OutOfBounds\Elastic\OutOfBoundsAnalogRepository;
use App\Repository\Sensor\OutOfBounds\Elastic\OutOfBoundsElasticQueryInterface;
use App\Repository\Sensor\OutOfBounds\Elastic\OutOfBoundsHumidityRepository;
use App\Repository\Sensor\OutOfBounds\Elastic\OutOfBoundsLatitudeRepository;
use App\Repository\Sensor\OutOfBounds\Elastic\OutOfBoundsTempRepository;
use Elastica\Client;
use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\MatchAll;
use Elastica\Query\Range;
use Elastica\Query\Term;
use Elastica\Search;

class OutOfBoundsElasticSearchService
{
    /** @var array<string, OutOfBoundsElasticQueryInterface> */
    private array $repositories;

    public function __construct(
        private readonly Client $client,
        private readonly OutOfBoundsTempRepository $tempRepository,
        private readonly OutOfBoundsHumidityRepository $humidityRepository,
        private readonly OutOfBoundsAnalogRepository $analogRepository,
        private readonly OutOfBoundsLatitudeRepository $latitudeRepository,
    ) {
        $this->repositories = [
            'temperature' => $this->tempRepository,
            'humidity' => $this->humidityRepository,
            'analog' => $this->analogRepository,
            'latitude' => $this->latitudeRepository,
        ];
    }

    /**
     * @return OutOfBoundsReadingResponseDTO[]
     */
    public function search(GetOutOfBoundsReadingsRequestDTO $dto): array
    {
        $readingTypes = $dto->getReadingTypes();
        $query = $this->buildQuery($dto);
        $results = [];

        if (count($readingTypes) === 1) {
            $readingType = $readingTypes[0];
            $hits = $this->repositories[$readingType]->search($query);

            foreach ($hits as $hit) {
                $results[] = OutOfBoundsReadingResponseDTO::fromElasticHit($hit, $readingType);
            }

            return $results;
        }

        // Multi-index search
        $indexToReadingType = [];
        $search = new Search($this->client);

        foreach ($readingTypes as $readingType) {
            $index = $this->repositories[$readingType]->getIndex();
            $search->addIndex($index);
            $indexToReadingType[$index->getName()] = $readingType;
        }

        $resultSet = $search->search($query);

        foreach ($resultSet->getResults() as $result) {
            $indexName = $result->getIndex();
            $readingType = $indexToReadingType[$indexName] ?? null;

            if ($readingType !== null) {
                $results[] = OutOfBoundsReadingResponseDTO::fromElasticHit($result->getData(), $readingType);
            }
        }

        return $results;
    }

    private function buildQuery(GetOutOfBoundsReadingsRequestDTO $dto): Query
    {
        $query = new Query();
        $query->setSize($dto->getLimit());
        $query->setFrom($dto->getOffset());

        // sensorReadingID takes highest priority
        if ($dto->getSensorReadingID() !== null) {
            $query->setQuery(new Term(['sensorReadingID' => $dto->getSensorReadingID()]));

            return $query;
        }

        $boolQuery = new BoolQuery();
        $hasFilter = false;

        // Threshold filter
        if ($dto->getThreshold() !== null && $dto->getDirection() !== null) {
            $rangeParams = $dto->getDirection() === GetOutOfBoundsReadingsRequestDTO::DIRECTION_ABOVE
                ? ['gte' => $dto->getThreshold()]
                : ['lte' => $dto->getThreshold()];

            $boolQuery->addMust(new Range('sensorReading', $rangeParams));
            $hasFilter = true;
        }

        // Date range filter
        if ($dto->getStartDate() !== null && $dto->getEndDate() !== null) {
            $boolQuery->addMust(new Range('createdAt', [
                'gte' => $dto->getStartDate()->format(\DateTimeInterface::ATOM),
                'lte' => $dto->getEndDate()->format(\DateTimeInterface::ATOM),
            ]));
            $hasFilter = true;
        }

        $query->setQuery($hasFilter ? $boolQuery : new MatchAll());

        return $query;
    }
}
