<?php

namespace Types;

class FloatType extends IntegerType
{
    public function __invoke(): float
    {
        return (float) $this->value;
    }
}
