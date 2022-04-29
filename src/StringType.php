<?php

namespace Types;

class StringType extends AbstractType
{
    public function __toString(): string
    {
        return $this();
    }
}
