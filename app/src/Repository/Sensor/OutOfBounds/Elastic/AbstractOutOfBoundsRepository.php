<?php

namespace App\Repository\Sensor\OutOfBounds\Elastic;

use App\Builders\Sensor\Internal\ReadingType\ReadingTypeCreationBuilders\SensorOutOfBoundsCreationBuilders\Elastic\OutOfBoundsPersistenceDTOBuilder;
use App\DTOs\Sensor\Request\OutOfBounds\Elastic\OutOfBoundsElasticPersistenceDTO;
use App\Entity\Sensor\OutOfRangeRecordings\OutOfBoundsEntityInterface;
use Elastica\Document;
use Elastica\Index;
use Symfony\Component\Serializer\SerializerInterface;

abstract class AbstractOutOfBoundsRepository
{
    protected Index $index;

    protected SerializerInterface $serializer;

    public function __construct(Index $index, SerializerInterface $serializer)
    {
        $this->index = $index;
        $this->serializer = $serializer;
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
}
