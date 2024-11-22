<?php

namespace App\Repository\Sensor\ConstRecord\Elastic;

use App\Builders\Sensor\Internal\ReadingType\ReadingTypeCreationBuilders\ConstRecordCreationBuilders\Elastic\ConstRecordPersistenceDTOBuilder;
use App\DTOs\Sensor\Request\ConstRecord\Elastic\ConstRecordElasticPersistenceDTO;
use App\Entity\Sensor\ConstantRecording\ConstantlyRecordEntityInterface;
use Elastica\Document;
use Elastica\Index;
use Symfony\Component\Serializer\SerializerInterface;

abstract class AbstractConstRecordRepository
{
    protected Index $index;

    protected SerializerInterface $serializer;

    public function __construct(Index $index, SerializerInterface $serializer)
    {
        $this->index = $index;
        $this->serializer = $serializer;
    }

    protected function serializeOutOfBoundsEntity(ConstRecordElasticPersistenceDTO $outOfBoundsEntity): array
    {
        return $this->serializer->normalize($outOfBoundsEntity, 'json');
    }

    public function persist(ConstantlyRecordEntityInterface $outOfBoundsEntity): void
    {
        $outOfBoundsUpdateDTO = ConstRecordPersistenceDTOBuilder::buildConstRecordElasticPersistenceDTO($outOfBoundsEntity);

        $serializedUpdateDTO = $this->serializeOutOfBoundsEntity($outOfBoundsUpdateDTO);
        $this->index->addDocument(
            new Document(null, $serializedUpdateDTO)
        );

        $this->index->refresh();
    }
}
