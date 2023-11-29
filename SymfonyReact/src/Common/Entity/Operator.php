<?php

namespace App\Common\Entity;

use App\Common\Repository\OperatorRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ORM\Entity(repositoryClass: OperatorRepository::class),
    ORM\Table(name: "operators"),
]
class Operator
{
    public const OPERATOR_EQUAL = '==';

    public const OPERATOR_NOT_EQUAL = '!=';

    public const OPERATOR_GREATER_THAN_OR_EQUAL = '>=';

    public const OPERATOR_LESS_THAN_OR_EQUAL = '<=';

    public const OPERATOR_GREATER_THAN = '>';

    public const OPERATOR_LESS_THAN = '<';

    #[
        ORM\Column(name: "operatorID", type: "integer", nullable: false),
        ORM\Id,
        ORM\GeneratedValue(strategy: "IDENTITY"),
    ]
    private int $operatorID;

    #[
        ORM\Column(name: "operatorName", type: "string", length: 255, nullable: false),
        Assert\NotBlank(message: "Operator name cannot be blank"),
        Assert\Length(
            min: 3,
            max: 255,
            minMessage: "Operator name must be at least {{ limit }} characters long",
            maxMessage: "Operator name cannot be longer than {{ limit }} characters"
        ),
    ]
    private string $operatorName;

    #[
        ORM\Column(name: "operatorSymbol", type: "string", length: 255, nullable: false),
        Assert\NotBlank(message: "Operator symbol cannot be blank"),
        Assert\Length(
            min: 1,
            max: 255,
            minMessage: "Operator symbol must be at least {{ limit }} characters long",
            maxMessage: "Operator symbol cannot be longer than {{ limit }} characters"
        ),
    ]
    private string $operatorSymbol;

    #[
        ORM\Column(name: "operatorDescription", type: "string", length: 255, nullable: false),
        Assert\NotBlank(message: "Operator description cannot be blank"),
        Assert\Length(
            min: 3,
            max: 255,
            minMessage: "Operator description must be at least {{ limit }} characters long",
            maxMessage: "Operator description cannot be longer than {{ limit }} characters"
        ),
    ]
    private string $operatorDescription;

    public function getOperatorID(): int
    {
        return $this->operatorID;
    }

    public function getOperatorName(): string
    {
        return $this->operatorName;
    }

    public function setOperatorName(string $operatorName): void
    {
        $this->operatorName = $operatorName;
    }

    public function getOperatorSymbol(): string
    {
        return $this->operatorSymbol;
    }

    public function setOperatorSymbol(string $operatorSymbol): void
    {
        $this->operatorSymbol = $operatorSymbol;
    }

    public function getOperatorDescription(): string
    {
        return $this->operatorDescription;
    }

    public function setOperatorDescription(string $operatorDescription): void
    {
        $this->operatorDescription = $operatorDescription;
    }
}
