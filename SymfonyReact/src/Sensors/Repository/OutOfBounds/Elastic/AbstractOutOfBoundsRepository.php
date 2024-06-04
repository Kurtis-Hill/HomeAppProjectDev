<?php

namespace App\Sensors\Repository\OutOfBounds\Elastic;

use App\Sensors\DTO\Request\OutOfBounds\Elastic\OutOfBoundsElasticPersistenceDTO;
use App\Sensors\Entity\OutOfRangeRecordings\OutOfBoundsEntityInterface;
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
        $outOfBoundsUpdateDTO = \App\Sensors\Builders\Internal\ReadingType\ReadingTypeCreationBuilders\SensorOutOfBoundsCreationBuilders\Elastic\OutOfBoundsPersistenceDTOBuilder::buildOutOfBoundsPersistenceDTO($outOfBoundsEntity);

        $serializedUpdateDTO = $this->serializeOutOfBoundsEntity($outOfBoundsUpdateDTO);
        $this->index->addDocument(
            new Document(null, $serializedUpdateDTO)
        );

        $this->index->refresh();
    }
}
