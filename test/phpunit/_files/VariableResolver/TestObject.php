<?php
/**
 * @link    https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\PhpUnit\Data\VariableResolver;

/**
 * Class TestObject
 *
 * @package OldTown\Workflow\PhpUnit\Data\VariableResolver
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
}