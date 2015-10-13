<?php
/**
 * @link    https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\PhpUnitTest\Loader;

use PHPUnit_Framework_TestCase as TestCase;
use OldTown\Workflow\Loader\Traits\ArgsTrait;
use OldTown\Workflow\Loader\Traits\ArgsInterface;
use ReflectionClass;

/**
 * Class ArgsTraitTest
 *
 * @package OldTown\Workflow\PhpUnitTest\Loader
 */
class ArgsTraitTest extends TestCase
{
    /**
     * @var ArgsInterface
     */
    private $objArg;

    /**
     *
     * @return void
     */
    protected function setUp()
    {
        $this->objArg = $this->getMockForTrait(ArgsTrait::class);
    }


    /**
     * Трейт для работы с аргументами должен реализовывать интерфейс ArgsInterface
     *
     * @return void
     */
    public function testTraitImplementsMethod()
    {
        $r = new ReflectionClass(ArgsInterface::class);
        $methods = $r->getMethods();

        foreach ($methods as $method) {
            $methodName = $method->getName();
            $flagHasMethod = method_exists($this->objArg, $methodName);
            $errMsg = sprintf('Trait %s not implement method %s', ArgsTrait::class, $methodName);
            static::assertTrue($flagHasMethod, $errMsg);
        }
    }


    /**
     * Возвращает аргументы
     *
     * @return void
     */
    public function testGetArgs()
    {
        $expectedArgs = [
            'name1' => 'value1',
            'name2' => 'value2',
        ];

        foreach ($expectedArgs as $key => $value) {
            $this->objArg->setArg($key, $value);
        }

        $actualArgs = $this->objArg->getArgs();

        static::assertEquals($expectedArgs, $actualArgs);
    }

    /**
     * Устанавливает аргумент
     *
     *
     * @return void
     */
    public function testSetArg()
    {
        $expectedValue = 'test';
        $this->objArg->setArg('test', $expectedValue);
        $actualValue = $this->objArg->getArg('test', null);

        static::assertEquals($expectedValue, $actualValue);
    }


    /**
     * Устанавливает аргумент
     *
     *
     * @return void
     */
    public function testGetArgDefaultValue()
    {
        $expectedValue = 'defaultValue';
        $actualValue = $this->objArg->getArg('test', $expectedValue);

        static::assertEquals($expectedValue, $actualValue);
    }
}
