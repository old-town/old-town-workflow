<?php
/**
 * @link    https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTownWorkflowBehatTestData\VariableResolver;

/**
 * Class TestObject
 *
 * @package OldTownWorkflowBehatTestData\VariableResolver
 */
class TestObject
{
    /**
     * @return $this
     */
    public function getValue1()
    {
        return $this;
    }

    /**
     * @return string
     */
    public function getValue2()
    {
        return 'value_2';
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return 'test_string_value';
    }
}
