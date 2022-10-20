<?php

namespace App\Sensors\Command\Elasticsearch\IndicesCreation;

use App\Sensors\Repository\ConstRecord\Elastic\AbstractConstRecordRepository;
use Elastica\Client;
use Elastica\Exception\InvalidException;
use Elastica\Exception\ResponseException;
use Elastica\Mapping;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ElasticCreateConstRecordIndicesCommand extends Command
{
    protected static $defaultName = 'app:elastic-create-const-record-indices';

    protected Client $elasticSearchClient;

    public function __construct(Client $client, string $name = null, array $indexMappings = [])
    {
        dd($indexMappings);
        $this->elasticSearchClient = $client;
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Creates the indices for the constant record types in ElasticSearch')
            ->setHelp('This command creates the indices for the constant record types in ElasticSearch');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            'ElasticSearch Constant Record Indices',
            '======================================',
        ]);

        $output->writeln('Creating indices...');

        foreach (AbstractConstRecordRepository::CONST_RECORD_INDICES as $indexName => $mappingProperties) {
            $output->writeln("Creating index: $indexName");
            $index = $this->elasticSearchClient->getIndex($indexName);

            if ($this->elasticSearchClient->getIndex($indexName)->exists()) {
                $output->writeln("<info>Index already exists: $indexName</info>");
                continue;
            }

            try {
                $index->create([], ['recreate' => false]);
            } catch (InvalidException|ResponseException $e) {
                $output->writeln('<error>' . $e->getMessage() . '</error>');
                continue;
            }

            $mapping = new Mapping();
            $mapping->setProperties([
                'outOfRangeID' => ['type' => 'integer'],
                $mappingProperties['sensorFieldName'] => ['type' => 'integer'],
                'sensorReading' => ['type' => $mappingProperties['sensorReading']],
                'createdAt' => ['type' => 'date'],
            ]);

            $mapping->send($index);
            $output->writeln("<info>Index created: $indexName</info>");
        }

        $output->writeln('<info>Indices created successfully!</info>');

        return Command::SUCCESS;
    }
}
