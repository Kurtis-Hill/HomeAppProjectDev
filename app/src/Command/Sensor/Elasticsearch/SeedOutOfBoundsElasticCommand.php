<?php

declare(strict_types=1);

namespace App\Command\Sensor\Elasticsearch;

use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Analog;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Humidity;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Latitude;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Temperature;
use App\Repository\Sensor\OutOfBounds\Elastic\OutOfBoundsAnalogRepository;
use App\Repository\Sensor\OutOfBounds\Elastic\OutOfBoundsHumidityRepository;
use App\Repository\Sensor\OutOfBounds\Elastic\OutOfBoundsLatitudeRepository;
use App\Repository\Sensor\OutOfBounds\Elastic\OutOfBoundsTempRepository;
use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;
use Elastica\Document;
use Elastica\Index;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:elastic:seed-out-of-bounds',
    description: 'Seeds the out-of-bounds Elasticsearch indices with randomised test data for development / manual testing.',
)]
class SeedOutOfBoundsElasticCommand extends Command
{
    /**
     * Starting ID for synthetic sensor-reading IDs.
     * Kept high to avoid clashing with real database IDs if the environment also has live data.
     */
    private const ID_OFFSET = 100_000;

    /**
     * Out-of-bounds reading ranges per type.
     * Each entry has two bands: [above_min, above_max] and [below_min, below_max].
     */
    private const READING_RANGES = [
        Temperature::READING_TYPE => [
            'above' => [85.0,  150.0],   // above the ~80-85°C sensor high bound
            'below' => [-70.0, -41.0],   // below the ~-40/-55°C sensor low bound
            'unit'  => '°C',
        ],
        Humidity::READING_TYPE => [
            'above' => [100.5, 130.0],   // above 100 % max
            'below' => [-30.0,  -0.5],   // below 0 % min
            'unit'  => '%',
        ],
        Analog::READING_TYPE => [
            'above' => [10_000.0, 15_000.0],   // above 9 999 Soil/LDR high bound
            'below' => [0.0,        999.0],    // below 1 000 Soil low bound
            'unit'  => '',
        ],
        Latitude::READING_TYPE => [
            'above' => [91.0,  180.0],   // above 90° max
            'below' => [-180.0, -91.0],  // below -90° min
            'unit'  => '°',
        ],
    ];

    public function __construct(
        private readonly OutOfBoundsTempRepository     $tempRepository,
        private readonly OutOfBoundsHumidityRepository $humidityRepository,
        private readonly OutOfBoundsAnalogRepository   $analogRepository,
        private readonly OutOfBoundsLatitudeRepository $latitudeRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption(
                'count',
                'c',
                InputOption::VALUE_OPTIONAL,
                'Number of documents to generate per reading type.',
                50,
            )
            ->addOption(
                'days',
                'd',
                InputOption::VALUE_OPTIONAL,
                'Spread documents evenly over the past N days.',
                30,
            )
            ->addOption(
                'reading-types',
                'r',
                InputOption::VALUE_OPTIONAL,
                sprintf(
                    'Comma-separated list of reading types to seed (%s). Defaults to all.',
                    implode(', ', array_keys(self::READING_RANGES)),
                ),
                implode(',', array_keys(self::READING_RANGES)),
            )
            ->addOption(
                'clear',
                null,
                InputOption::VALUE_NONE,
                'Delete all existing documents from the target indices before seeding.',
            )
            ->addOption(
                'id-offset',
                null,
                InputOption::VALUE_OPTIONAL,
                'Starting sensorReadingID for the synthetic documents.',
                self::ID_OFFSET,
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Out-of-Bounds Elasticsearch Seeder');

        // ── Resolve options ──────────────────────────────────────────────────
        $count    = max(1, (int) $input->getOption('count'));
        $days     = max(1, (int) $input->getOption('days'));
        $idOffset = max(0, (int) $input->getOption('id-offset'));
        $doClear  = (bool) $input->getOption('clear');

        $requestedTypes = array_filter(
            array_map('trim', explode(',', (string) $input->getOption('reading-types'))),
        );

        $invalidTypes = array_diff($requestedTypes, array_keys(self::READING_RANGES));
        if (!empty($invalidTypes)) {
            $io->error(sprintf(
                'Unknown reading type(s): %s. Valid types: %s',
                implode(', ', $invalidTypes),
                implode(', ', array_keys(self::READING_RANGES)),
            ));

            return Command::FAILURE;
        }

        $repositoryMap = $this->buildRepositoryMap();

        $io->definitionList(
            ['Documents per type' => $count],
            ['Date spread (days)' => $days],
            ['Reading types'      => implode(', ', $requestedTypes)],
            ['Clear first'        => $doClear ? 'yes' : 'no'],
            ['ID offset'          => $idOffset],
        );

        // ── Process each index ───────────────────────────────────────────────
        $globalId = $idOffset;

        foreach ($requestedTypes as $readingType) {
            $repo      = $repositoryMap[$readingType];
            $index     = $repo->getIndex();
            $ranges    = self::READING_RANGES[$readingType];

            $io->section(sprintf('Seeding "%s" index: %s', $readingType, $index->getName()));

            if ($doClear) {
                $this->clearIndex($index, $io);
            }

            $documents = $this->generateDocuments(
                count:      $count,
                days:       $days,
                idOffset:   $globalId,
                ranges:     $ranges,
            );

            $this->pushDocuments($index, $documents, $output, $io);

            $io->success(sprintf(
                'Pushed %d documents into "%s" (IDs %d – %d).',
                $count,
                $index->getName(),
                $globalId,
                $globalId + $count - 1,
            ));

            $globalId += $count;
        }

        $io->success('All indices seeded successfully.');

        return Command::SUCCESS;
    }

    // ── Private helpers ──────────────────────────────────────────────────────

    /**
     * @return array<string, \App\Repository\Sensor\OutOfBounds\Elastic\AbstractOutOfBoundsRepository>
     */
    private function buildRepositoryMap(): array
    {
        return [
            Temperature::READING_TYPE => $this->tempRepository,
            Humidity::READING_TYPE    => $this->humidityRepository,
            Analog::READING_TYPE      => $this->analogRepository,
            Latitude::READING_TYPE    => $this->latitudeRepository,
        ];
    }

    /**
     * Generate $count Elastica Documents spread over the past $days days.
     * Roughly half will be above-boundary, half below-boundary, with some
     * random scatter so threshold filtering tests are interesting.
     *
     * @param array{above: float[], below: float[], unit: string} $ranges
     * @return Document[]
     */
    private function generateDocuments(
        int   $count,
        int   $days,
        int   $idOffset,
        array $ranges,
    ): array {
        $documents     = [];
        $now           = new DateTimeImmutable('now');
        $totalSeconds  = $days * 86_400;

        for ($i = 0; $i < $count; $i++) {
            // Spread timestamps evenly with a little jitter (±5 minutes)
            $secondsAgo = (int) (($i / max(1, $count - 1)) * $totalSeconds);
            $jitter     = random_int(-300, 300);
            $createdAt  = $now->sub(new DateInterval(
                sprintf('PT%dS', max(0, $secondsAgo - $jitter)),
            ));

            // Alternate above/below; the final ~10 % sit right at or near the boundary
            // so you get a gradient for threshold-query testing.
            $reading = $this->pickReading($i, $count, $ranges);

            $documents[] = new Document(null, [
                'sensorReadingID' => $idOffset + $i,
                'sensorReading'   => $reading,
                'createdAt'       => $createdAt->format(DateTimeInterface::ATOM),
            ]);
        }

        return $documents;
    }

    /**
     * Pick a reading value that varies across the above/below bands so that
     * different threshold queries return different result counts.
     *
     * Distribution:
     *   i = 0 .. count/2-1   → above-boundary (spread from above_min to above_max)
     *   i = count/2 .. end   → below-boundary (spread from below_max to below_min)
     *
     * @param array{above: float[], below: float[], unit: string} $ranges
     */
    private function pickReading(int $i, int $totalCount, array $ranges): float
    {
        $half = (int) floor($totalCount / 2);

        if ($i < $half) {
            // Linearly spread through the above band
            $fraction = $half > 1 ? $i / ($half - 1) : 0.0;
            $value    = $ranges['above'][0] + $fraction * ($ranges['above'][1] - $ranges['above'][0]);
        } else {
            // Linearly spread through the below band (inverted so extreme values come last)
            $j        = $i - $half;
            $remain   = $totalCount - $half;
            $fraction = $remain > 1 ? $j / ($remain - 1) : 0.0;
            $value    = $ranges['below'][1] - $fraction * ($ranges['below'][1] - $ranges['below'][0]);
        }

        // Add ±1 % random noise so readings are not perfectly linear
        $noise = $value * (random_int(-100, 100) / 10_000);

        return round($value + $noise, 4);
    }

    /**
     * @param Document[] $documents
     */
    private function pushDocuments(Index $index, array $documents, OutputInterface $output, SymfonyStyle $io): void
    {
        $progress = new ProgressBar($output, count($documents));
        $progress->start();

        // Elastica supports bulk-add; chunk into batches of 200 to avoid huge payloads.
        foreach (array_chunk($documents, 200) as $chunk) {
            $index->addDocuments($chunk);
            $progress->advance(count($chunk));
        }

        $index->refresh();
        $progress->finish();
        $io->newLine();
    }

    private function clearIndex(Index $index, SymfonyStyle $io): void
    {
        $io->warning(sprintf('Clearing all documents from index "%s"…', $index->getName()));
        try {
            $index->deleteByQuery(new \Elastica\Query\MatchAll());
            $index->refresh();
            $io->comment('Index cleared.');
        } catch (\Throwable $e) {
            $io->warning(sprintf('Could not clear index (it may not exist yet): %s', $e->getMessage()));
        }
    }
}
