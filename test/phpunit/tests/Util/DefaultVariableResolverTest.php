<?php
/**
 * @link    https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\PhpUnitTest\Util;

use PHPUnit_Framework_TestCase as TestCase;
use OldTown\Workflow\TransientVars\BaseTransientVars;
use OldTown\PropertySet\PropertySetInterface;
use OldTown\Workflow\Util\DefaultVariableResolver;
use OldTown\Workflow\PhpUnit\Data\VariableResolver\TestObject;

/**
 * Class DefaultVariableResolverTest
 *
 * @package OldTown\Workflow\PhpUnitTest\Util
 */
class  DefaultVariableResolverTest extends TestCase
{
    /**
     * @var DefaultVariableResolver
     */
    protected $variableResolver;

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->variableResolver = new DefaultVariableResolver();
        parent::setUp();
    }

    /**
     * Проверка получения значения из TransientVars.
     *
     */
    public function testTranslateVariablesResolveFromTransientVars()
    {
        $tv = new BaseTransientVars();
        /** @var PropertySetInterface $ps */
        $ps = $this->getMock(PropertySetInterface::class);

        $var = '${testVariable}';

        $expected = new \stdClass();

        $tv['testVariable'] = $expected;
        $actual = $this->variableResolver->translateVariables($var, $tv, $ps);

        static::assertTrue($expected === $actual, $actual);
    }

    /**
     * Проверка замены нескольких переменных из TransientVars
     */
    public function testTranslateVariablesMultiVariables()
    {
        $str = 'text1 ${var1} text2 ${var2} ${var3}';

        $tv = new BaseTransientVars();
        $tv['var1'] = 'var1_value';
        $tv['var2'] = 'var2_value';
        $tv['var3'] = 'var3_value';

        /** @var PropertySetInterface $ps */
        $ps = $this->getMock(PropertySetInterface::class);

        $actual = $this->variableResolver->translateVariables($str, $tv, $ps);

        static::assertEquals('text1 var1_value text2 var2_value var3_value', $actual);
    }


    /**
     * Проверка работы с методами
     */
    public function testTranslateVariablesObjectMethod()
    {
        $tv = new BaseTransientVars();
        $tv['obj'] = new TestObject();


        /** @var PropertySetInterface $ps */
        $ps = $this->getMock(PropertySetInterface::class);

        $actual = $this->variableResolver->translateVariables('${obj.value1.value2}', $tv, $ps);

        static::assertEquals($tv['obj']->getValue1()->getValue2(), $actual);
    }
}
