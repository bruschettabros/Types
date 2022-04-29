<?php

namespace Types\Casting;

use phpDocumentor\Reflection\Types\Boolean;
use PhpParser\Node\Expr\Cast\Double;
use Types\AbstractType;
use Types\ArrayType;
use Types\BooleanType;
use Types\DoubleType;
use Types\Exceptions\UnavailableCastingException;
use Types\FloatType;
use Types\IntegerType;
use Types\StringType;

/**
 * @property ArrayType $array
 * @property StringType $string
 * @property BooleanType $bool
 * @property BooleanType $boolean
 * @property IntegerType $int
 * @property IntegerType $integer
 * @property DoubleType $double
 * @property FloatType $float
 */
class Cast
{
    public function __construct($value)
    {
        $this->value = $value;
    }

    public function __get($property)
    {
        if (method_exists(self::class, $property)) {
            return AbstractType::create($this->$property());
        }
        throw new UnavailableCastingException(sprintf('cannot cast to %s', $property));
    }

    private function array(): array
    {
        return (array) $this->value;
    }

    private function string(): string
    {
        return (string) $this->value;
    }

    private function int(): int
    {
        return (int) $this->value;
    }

    private function integer(): int
    {
        return $this->int();
    }

    private function bool(): boolean
    {
        return (bool) $this->value;
    }

    private function boolean(): boolean
    {
        return $this->bool();
    }

    private function double(): double
    {
        return (double) $this->value;
    }

    private function float(): float
    {
        return (float) $this->value;
    }
}
