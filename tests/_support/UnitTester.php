<?php

declare(strict_types=1);

use Codeception\Actor;

/**
 * Inherited Methods.
 *
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause()
 *
 * @SuppressWarnings(PHPMD)
 * @psalm-suppress UndefinedTrait
 */
class UnitTester extends Actor
{
    use _generated\UnitTesterActions;

    /**
     * @param  string  $className
     * @param  string  $propertyName
     * @param  object  $stub
     *
     * @throws ReflectionException
     *
     * @return mixed
     */
    public function getPrivatePropertyValue(string $className, string $propertyName, object $stub)
    {
        $reflector = new ReflectionClass($className);
        $property = $reflector->getProperty($propertyName);
        $property->setAccessible(true);

        return $property->getValue($stub);
    }

    /**
     * @param  mixed  $expected
     * @param  string  $className
     * @param  string  $propertyName
     * @param  object  $stub
     *
     * @throws ReflectionException
     */
    public function assertPrivatePropertyValue($expected, string $className, string $propertyName, object $stub)
    {
        $this->assertEquals($expected, $this->getPrivatePropertyValue($className, $propertyName, $stub));
    }
}
