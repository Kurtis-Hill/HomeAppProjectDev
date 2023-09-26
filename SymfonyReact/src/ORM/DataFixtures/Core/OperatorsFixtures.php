<?php

namespace App\ORM\DataFixtures\Core;

use App\Common\Entity\Operator;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class OperatorsFixtures extends Fixture
{
    public const EQUALS = 'Equals';

    public const NOT_EQUALS = 'Not Equals';

    public const GREATER_THAN = 'Greater Than';

    public const LESS_THAN = 'Less Than';

    public const OPERATORS = [
        self::EQUALS => [
            'operatorName' => self::EQUALS,
            'operatorSymbol' => '==',
            'operatorDescription' => self::EQUALS,
        ],
        self::NOT_EQUALS => [
            'operatorName' => self::NOT_EQUALS,
            'operatorSymbol' => '!=',
            'operatorDescription' => self::NOT_EQUALS,
        ],
        self::GREATER_THAN => [
            'operatorName' => self::GREATER_THAN,
            'operatorSymbol' => '>',
            'operatorDescription' => self::GREATER_THAN,
        ],
        self::LESS_THAN => [
            'operatorName' => self::LESS_THAN,
            'operatorSymbol' => '<',
            'operatorDescription' => self::LESS_THAN,
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::OPERATORS as $operator) {
            $operatorEntity = new Operator();
            $operatorEntity->setOperatorName($operator['operatorName']);
            $operatorEntity->setOperatorSymbol($operator['operatorSymbol']);
            $operatorEntity->setOperatorDescription($operator['operatorDescription']);

            $manager->persist($operatorEntity);
            $this->addReference($operator['operatorName'], $operatorEntity);
        }

        $manager->flush();
    }
}
