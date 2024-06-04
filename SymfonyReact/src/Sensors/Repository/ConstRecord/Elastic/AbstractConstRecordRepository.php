<?php

namespace App\Sensors\Repository\ConstRecord\Elastic;

use App\Sensors\DTO\Request\ConstRecord\Elastic\ConstRecordElasticPersistenceDTO;
use App\Sensors\Entity\ConstantRecording\ConstantlyRecordEntityInterface;
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
        $outOfBoundsUpdateDTO = \App\Sensors\Builders\Internal\ReadingType\ReadingTypeCreationBuilders\ConstRecordCreationBuilders\Elastic\ConstRecordPersistenceDTOBuilder::buildConstRecordElasticPersistenceDTO($outOfBoundsEntity);

        $serializedUpdateDTO = $this->serializeOutOfBoundsEntity($outOfBoundsUpdateDTO);
        $this->index->addDocument(
            new Document(null, $serializedUpdateDTO)
        );

        $this->index->refresh();
    }
}
