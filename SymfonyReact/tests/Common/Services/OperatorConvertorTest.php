<?php

namespace App\Tests\Common\Services;

use App\Common\Entity\Operator;
use App\Common\Exceptions\OperatorConvertionException;
use App\Common\Services\OperatorConvertor;
use Generator;
use PHPUnit\Framework\TestCase;

class OperatorConvertorTest extends TestCase
{
    private OperatorConvertor $sut;

    protected function setUp(): void
    {
        $this->sut = new OperatorConvertor();
        parent::setUp();
    }

    /**
     * @dataProvider stringBooleanDataProvider
     */
    public function test_string_boolean_can_be_compared_to_boolean(
        mixed $value,
        mixed $valueThatTriggers,
        bool $expectedResult,
    ): void {
        $operator = new Operator();
        $operator->setOperatorSymbol(Operator::OPERATOR_EQUAL);

        $result = $this->sut::convertUsingOperator(
            $operator,
            $value,
            $valueThatTriggers,
        );

        self::assertEquals($expectedResult, $result);
    }

    public function stringBooleanDataProvider(): Generator
    {
        yield [
            'value' => 'true',
            'valueThatTriggers' => true,
            'expectedResult' => true,
        ];

        yield [
            'value' => 'true',
            'valueThatTriggers' => false,
            'expectedResult' => false,
        ];

        yield [
            'value' => 'false',
            'valueThatTriggers' => false,
            'expectedResult' => true,
        ];

        yield [
            'value' => 'false',
            'valueThatTriggers' => true,
            'expectedResult' => false,
        ];
    }

    /**
     * @dataProvider numbersDataProvider
     */
    public function test_numbers_can_be_compared(
        mixed $value,
        mixed $valueThatTriggers,
        string $operatorSymbol,
        bool $expectedResult,
    ): void {
        $operator = new Operator();
        $operator->setOperatorSymbol($operatorSymbol);

        $result = $this->sut::convertUsingOperator(
            $operator,
            $value,
            $valueThatTriggers,
        );

        self::assertEquals($expectedResult, $result);
    }

    public function numbersDataProvider(): Generator
    {
        yield [
            'value' => '1234',
            'valueThatTriggers' => 1234,
            'operator' => Operator::OPERATOR_EQUAL,
            'expectedResult' => true,
        ];

        yield [
            'value' => '1234',
            'valueThatTriggers' => 1234,
            'operator' => Operator::OPERATOR_GREATER_THAN_OR_EQUAL,
            'expectedResult' => true,
        ];

        yield [
            'value' => 1234,
            'valueThatTriggers' => '1234',
            'operator' => Operator::OPERATOR_LESS_THAN_OR_EQUAL,
            'expectedResult' => true,
        ];

        yield [
            'value' => 1234,
            'valueThatTriggers' => '1234',
            'operator' => Operator::OPERATOR_EQUAL,
            'expectedResult' => true,
        ];

        yield [
            'value' => 1234,
            'valueThatTriggers' => '1234',
            'operator' => Operator::OPERATOR_GREATER_THAN_OR_EQUAL,
            'expectedResult' => true,
        ];

        yield [
            'value' => 1234,
            'valueThatTriggers' => '1234',
            'operator' => Operator::OPERATOR_LESS_THAN_OR_EQUAL,
            'expectedResult' => true,
        ];

        yield [
            'value' => '1234',
            'valueThatTriggers' => 1234,
            'operator' => Operator::OPERATOR_GREATER_THAN,
            'expectedResult' => false,
        ];

        yield [
            'value' => '1234',
            'valueThatTriggers' => 1234,
            'operator' => Operator::OPERATOR_LESS_THAN,
            'expectedResult' => false,
        ];

        yield [
            'value' => '1234',
            'valueThatTriggers' => 1234,
            'operator' => Operator::OPERATOR_GREATER_THAN,
            'expectedResult' => false,
        ];

        yield [
            'value' => 1234,
            'valueThatTriggers' => '1234',
            'operator' => Operator::OPERATOR_LESS_THAN,
            'expectedResult' => false,
        ];
    }

    public function test_passing_operator_that_doesnt_exist(): void
    {
        $operatorSymbole = '===';

        $operator = new Operator();
        $operator->setOperatorSymbol($operatorSymbole);

        $this->expectException(OperatorConvertionException::class);

        $result = $this->sut::convertUsingOperator(
            $operator,
            null,
            null,
        );
    }
}
