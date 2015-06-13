<?php
/**
 * @link    https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Test\Basic;

use PHPUnit_Framework_TestCase as TestCase;


/**
 * Class BasicWorkflowTest
 * @package OldTown\Workflow\Test\Basic
 */
class BasicWorkflowTest extends TestCase
{
    /**
     * Инициализация workflow. Workflow нужно иницаилизровать прежде, чем выполнять какие либо действия.
     * Workflow может быть инициализированно только один раз
     *
     * @param string $workflowName Имя workflow
     * @param integer $initialAction Имя первого шага, с которого начинается workflow
     * @param array $inputs Данные введеные пользователем
     * @return integer
     */
    public function testInitialize()
    {

    }
}
