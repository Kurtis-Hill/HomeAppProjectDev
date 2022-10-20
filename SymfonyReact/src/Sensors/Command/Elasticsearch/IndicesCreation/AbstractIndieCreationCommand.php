<?php

namespace App\Sensors\Command\Elasticsearch\IndicesCreation;

use App\Sensors\Clients\ElasticSearch\ElasticSearchClient;
use Elastica\Client;
use Symfony\Component\Console\Command\Command;

class AbstractIndieCreationCommand extends Command
{
    protected static $defaultName = 'app:elastic-create-abstract';

    protected const INDICES_TO_CREATE = [
        'temperature' => [
            'sensorFieldName' => 'tempID',
            'sensorReading' => 'float',
        ],
        'humidity' => [
            'sensorFieldName' => 'humidID',
            'sensorReading' => 'float',
        ],
        'latitude' => [
            'sensorFieldName' => 'latID',
            'sensorReading' => 'double',
        ],
        'analog' => [
            'sensorFieldName' => 'analogID',
            'sensorReading' => 'float',
        ],
    ];

    protected Client $elasticSearchClient;

    public function __construct(ElasticSearchClient $elasticSearchClient, string $name = null)
    {
        $this->elasticSearchClient = $elasticSearchClient->getElasticsearchClient();
        parent::__construct($name);
    }
}
