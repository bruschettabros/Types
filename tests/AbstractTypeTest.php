<?php


use Types\AbstractType;
use PHPUnit\Framework\TestCase;
use Types\ArrayType;
use Types\BooleanType;
use Types\DoubleType;
use Types\Exceptions\MethodNotSupportedException;
use Types\Exceptions\UnavailableCastingException;
use Types\FloatType;
use Types\IntegerType;
use Types\StringType;

class AbstractTypeTest extends TestCase
{

    public function testCreate(): void
    {
        $this->assertInstanceOf(StringType::class, AbstractType::create('string'));
        $this->assertInstanceOf(IntegerType::class, AbstractType::create(1));
        $this->assertInstanceOf(DoubleType::class, AbstractType::create(1.1));
        $this->assertInstanceOf(ArrayType::class, AbstractType::create(['string', 1]));
        $this->assertInstanceOf(BooleanType::class, AbstractType::create((bool) 1));
    }

    public function testInvoke(): void
    {
        $stringObject = AbstractType::create('string');
        $this->assertEquals('string', $stringObject());
    }

    public function testCall(): void
    {
        $object = AbstractType::create('string');
        $objectUpper = AbstractType::create('STRING');
        $this->assertEquals($objectUpper, $object->strtoupper());

        $objectContains = AbstractType::create('rin');
        $result = $object->str_contains($objectContains);
        $this->assertInstanceOf(BooleanType::class, $result);

        $this->expectException(MethodNotSupportedException::class);
        $this->expectErrorMessage('pow is not a supported method on ' . StringType::class);
        $object->pow(8);
    }

    public function testCast(): void
    {
        $object = AbstractType::create('string');
        $arrayObject = AbstractType::create(['string']);
        $array = $object->to->array;

        $this->assertInstanceOf(ArrayType::class, $array);
        $this->assertEquals($arrayObject, $array);
    }

    public function testUnavailableCast(): void
    {
        $this->expectException(UnavailableCastingException::class);

        $object = AbstractType::create('string');
        $object->to->invalid;
    }

    public function testMagicGet()
    {
        $object = AbstractType::create('    string');
        $this->assertEquals('string', $object->ltrim->scalar);
    }

    public function testMixedArguments(): void
    {
        $object = AbstractType::create('explode.string');
        $this->assertEquals(['explode', 'string'], $object->explode('.')->scalar);
    }
}
