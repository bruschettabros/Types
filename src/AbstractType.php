<?php

namespace Types;

use Types\Casting\Cast;
use Types\Exceptions\MethodNotSupportedException;
use Types\Exceptions\TypeDoesNotExistException;
use Types\FunctionProcessing\FunctionArgumentSwitches;

abstract class AbstractType
{
    public mixed $scalar;
    public Cast $to;

    /**
     * Creates a Type object based on the scalar type
     *
     * @param $scalar
     * @return mixed
     * @throws TypeDoesNotExistException
     */
    public static function create($scalar): self
    {
        $class = __NAMESPACE__ . '\\' . ucfirst(gettype($scalar)) . 'Type';
        if (class_exists($class)) {
            return new $class($scalar);
        }
        throw new TypeDoesNotExistException(sprintf('%s Is not a valid Type', $class));
    }

    public function __construct($scalar)
    {
        $this->scalar = $scalar;
        $this->to = new Cast($this->scalar);
    }

    public function __invoke()
    {
        return $this->scalar();
    }

    public function scalar(): mixed
    {
        return $this->scalar;
    }

    public function __get($property)
    {
        return $this->$property();
    }

    public function __call(string $name, array $arguments)
    {
        $arguments = $this->scalarArguments($arguments);
        try {
            return self::create(call_user_func_array($name, $this->sortArguments($name, $arguments)));
        } catch (\TypeError $e) {
            echo $e->getmessage() . PHP_EOL;
            throw new MethodNotSupportedException(sprintf('%s is not a supported method on %s', $name, static::class));
        }
    }

    private function scalarArguments(array $arguments): array
    {
        foreach ($arguments as &$argument) {
            if ($argument instanceof self) {
                $argument = $argument->scalar;
            }
        }
        return $arguments;
    }

    private function sortArguments(string $function, array $arguments): array
    {
        $argumentsOriginal = $arguments;

        if (array_key_exists($function, FunctionArgumentSwitches::GOTCHA)) {
            $arguments = array_slice($arguments,  FunctionArgumentSwitches::GOTCHA[$function]);
        }
        array_unshift($arguments, $this->scalar);

        foreach (array_diff($argumentsOriginal, $arguments) as $argument) {
            array_unshift($arguments, $argument);
        }
        return $arguments;
    }
}
