<?php

namespace App\Services\Logs;

use App\Factories\Common\ElasticLogDTOFactory;
use App\Traits\HomeAppAPITrait;
use Elastica\Document;
use Elastica\Index;
use Psr\Log\LoggerInterface;
use Stringable;

/**
* Just need to add ElasticLogStash handler to finish this off and use these rather than the build in logger
 */
class ElasticLogger implements LoggerInterface
{
    use HomeAppAPITrait;

    private Index $emergencyIndex;

    private Index $alertIndex;

    private Index $criticalIndex;

    private Index $errorIndex;

    private Index $warningIndex;

    private Index $noticeIndex;

    private Index $infoIndex;

    private Index $debugIndex;

    private Index $logIndex;

    private ElasticLogDTOFactory $elasticLogDTOFactory;

    public function __construct(
        Index $emergencyIndex,
        Index $alertIndex,
        Index $criticalIndex,
        Index $errorIndex,
        Index $warningIndex,
        Index $noticeIndex,
        Index $infoIndex,
        Index $debugIndex,
        Index $logIndex,
        ElasticLogDTOFactory $elasticLogDTOFactory
    ) {
        $this->emergencyIndex = $emergencyIndex;
        $this->alertIndex = $alertIndex;
        $this->criticalIndex = $criticalIndex;
        $this->errorIndex = $errorIndex;
        $this->warningIndex = $warningIndex;
        $this->noticeIndex = $noticeIndex;
        $this->infoIndex = $infoIndex;
        $this->debugIndex = $debugIndex;
        $this->logIndex = $logIndex;
        $this->elasticLogDTOFactory = $elasticLogDTOFactory;
    }

    public function emergency(Stringable|string $message, array $context = []): void
    {
        $elasticDTO = $this->elasticLogDTOFactory
            ->getElasticDTOBuilder('emergency')
            ->buildLogDTO($message, $context);

        $this->emergencyIndex->addDocument(
            new Document(null, $this->normalize($elasticDTO))
        );
    }

    public function alert(Stringable|string $message, array $context = []): void
    {
        $elasticDTO = $this->elasticLogDTOFactory
            ->getElasticDTOBuilder('alert')
            ->buildLogDTO($message, $context);

        $this->alertIndex->addDocument(
            new Document(null, $this->normalize($elasticDTO))
        );
    }

    public function critical(Stringable|string $message, array $context = []): void
    {
        $elasticDTO = $this->elasticLogDTOFactory
            ->getElasticDTOBuilder('critical')
            ->buildLogDTO($message, $context);

        $this->criticalIndex->addDocument(
            new Document(null, $this->normalize($elasticDTO))
        );
    }

    public function error(Stringable|string $message, array $context = []): void
    {
        $elasticDTO = $this->elasticLogDTOFactory
            ->getElasticDTOBuilder('error')
            ->buildLogDTO('MEMEMEMEME', $context);

            $this->errorIndex->addDocument(
                new Document(null, $this->normalize($elasticDTO))
            );
    }

    public function warning(Stringable|string $message, array $context = []): void
    {
        $elasticDTO = $this->elasticLogDTOFactory
            ->getElasticDTOBuilder('warning')
            ->buildLogDTO($message, $context);

        $this->warningIndex->addDocument(
            new Document(null, $this->normalize($elasticDTO))
        );
    }

    public function notice(Stringable|string $message, array $context = []): void
    {
        $elasticDTO = $this->elasticLogDTOFactory
            ->getElasticDTOBuilder('notice')
            ->buildLogDTO($message, $context);

        $this->noticeIndex->addDocument(
            new Document(null, $this->normalize($elasticDTO))
        );
    }

    public function info(Stringable|string $message, array $context = []): void
    {
        $elasticDTO = $this->elasticLogDTOFactory
            ->getElasticDTOBuilder('info')
            ->buildLogDTO($message, $context);

        $this->infoIndex->addDocument(
            new Document(null, $this->normalize($elasticDTO))
        );
    }

    public function debug(Stringable|string $message, array $context = []): void
    {
        $elasticDTO = $this->elasticLogDTOFactory
            ->getElasticDTOBuilder('debug')
            ->buildLogDTO($message, $context);

        $this->debugIndex->addDocument(
            new Document(null, $this->normalize($elasticDTO))
        );
    }

    public function log($level, Stringable|string $message, array $context = []): void
    {
        $elasticDTO = $this->elasticLogDTOFactory
            ->getElasticDTOBuilder($level)
            ->buildLogDTO($message, $context);

        $this->logIndex->addDocument(
            new Document(null, $this->normalize($elasticDTO))
        );
    }
}
