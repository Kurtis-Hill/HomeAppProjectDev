<?php

namespace App\Sensors\Command\Elasticsearch\IndicesCreation;

use Elastica\Exception\InvalidException;
use Elastica\Exception\ResponseException;
use Elastica\Mapping;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ElasticCreateConstRecordIndicesCommand extends AbstractIndieCreationCommand
{
    protected static $defaultName = 'app:elastic-create-const-record-indices';

    private const MAPPING_PROPERTY_INDEX = 'constrecord_';

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

        foreach (self::INDICES_TO_CREATE as $indexName => $mappingProperties) {
            $indexConcat = sprintf('%s%s', self::MAPPING_PROPERTY_INDEX, $indexName);
            $output->writeln("Creating index: $indexConcat");
            $index = $this->elasticSearchClient->getIndex($indexName);

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
            $output->writeln("<info>Index created: $indexConcat</info>");
        }

        $output->writeln('<info>Indices created successfully!</info>');

        return Command::SUCCESS;
    }
}
