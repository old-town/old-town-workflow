<?php
/**
 * Created by PhpStorm.
 * User: keanor
 * Date: 23.10.15
 * Time: 22:12
 */

namespace OldTown\Workflow\PhpUnitTest\Spi;

use DateTime;
use OldTown\Workflow\Spi\SimpleStep;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Тест для класса \OldTown\Workflow\Spi\SimpleStep
 * @package OldTown\Workflow\PhpUnitTest\Spi
 */
class SimpleStepTest extends TestCase
{
    /**
     * Проверяем что конструктор работает без фаталов
     */
    public function testConstruct()
    {
        $d = new DateTime();
        $simpleStep = $simpleStep = new SimpleStep(1, 1, 1, 1, 1, $d, $d, $d, 1, [1, 2, 3], 'c');

        $this->assertInstanceOf(SimpleStep::class, $simpleStep);
    }

    /**
     * @expectedException \OldTown\Workflow\Exception\ArgumentNotNumericException
     */
    public function testSetActionId()
    {
        $d = new DateTime();
        $step = new SimpleStep(1, 1, 1, 1, 1, $d, $d, $d, 1, [1, 2, 3], 'c');

        $refProp = new \ReflectionProperty(SimpleStep::class, 'actionId');
        $refProp->setAccessible(true);

        $step->setActionId(12);
        $this->assertEquals(12, $refProp->getValue($step));

        $step->setActionId('5a');
        $this->assertEquals(5, $refProp->getValue($step));

        // test type exception
        $step->setActionId('actionid');
    }

    /**
     * Проверяем getActionId
     */
    public function testGetActionId()
    {
        $d = new DateTime();
        $step = new SimpleStep(1, 1, 1, 4, 1, $d, $d, $d, 1, [1, 2, 3], 'c');
        $this->assertEquals(4, $step->getActionId());
    }

    /**
     * Проверяем установку caller
     */
    public function testSetCaller()
    {
        $d = new DateTime();
        $step = new SimpleStep(1, 1, 1, 1, 1, $d, $d, $d, 1, [1, 2, 3], 'c');

        $refProp = new \ReflectionProperty(SimpleStep::class, 'caller');
        $refProp->setAccessible(true);

        $step->setCaller('caller');
        $this->assertEquals('caller', $refProp->getValue($step));

        $step->setCaller();
        $this->assertNull($refProp->getValue($step));
    }

    /**
     * Проверяем получение caller
     */
    public function testGetCaller()
    {
        $d = new DateTime();
        $step = new SimpleStep(1, 1, 1, 1, 1, $d, $d, $d, 1, [1, 2, 3], 'c');

        $refProp = new \ReflectionProperty(SimpleStep::class, 'caller');
        $refProp->setAccessible(true);
        $refProp->setValue($step, 'test');
        $this->assertEquals('test', $step->getCaller());
    }

    /**
     * Проверяем установку dueDate
     */
    public function testSetDueDate()
    {
        $d = new DateTime();
        $step = new SimpleStep(1, 1, 1, 1, 1, $d, $d, $d, 1, [1, 2, 3], 'c');

        $refProp = new \ReflectionProperty(SimpleStep::class, 'dueDate');
        $refProp->setAccessible(true);

        $d1 = new DateTime('2012-11-12');
        $step->setDueDate($d1);
        $this->assertEquals($d1, $refProp->getValue($step));

        try {
            $step->setDueDate('incorrect date');
            $this->fail('Не сработала ошибка при установке некорректной даты');
        } catch (\Exception $e) {
        }
    }

    /**
     * Проверяем получение dueDate
     */
    public function testGetDueDate()
    {
        $d = new DateTime();
        $step = new SimpleStep(1, 1, 1, 1, 1, $d, $d, $d, 1, [1, 2, 3], 'c');

        $refProp = new \ReflectionProperty(SimpleStep::class, 'dueDate');
        $refProp->setAccessible(true);
        $testDate = new DateTime('2015-11-22');
        $refProp->setValue($step, $testDate);
        $this->assertEquals($testDate, $step->getDueDate());
    }

    /**
     * @expectedException \OldTown\Workflow\Exception\ArgumentNotNumericException
     */
    public function testSetEntryId()
    {
        $d = new DateTime();
        $step = new SimpleStep(1, 1, 1, 1, 1, $d, $d, $d, 1, [1, 2, 3], 'c');

        $refActionProp = new \ReflectionProperty(SimpleStep::class, 'entryId');
        $refActionProp->setAccessible(true);

        $refActionProp->setValue($step, 4);
        $this->assertEquals(4, $refActionProp->getValue($step));

        // test not int exception
        $step->setEntryId('something');
    }

    /**
     * Проверяем получение entryId
     */
    public function testGetEntryId()
    {
        $d = new DateTime();
        $step = new SimpleStep(1, 1, 1, 1, 1, $d, $d, $d, 1, [1, 2, 3], 'c');

        $refEntryProp = new \ReflectionProperty(SimpleStep::class, 'entryId');
        $refEntryProp->setAccessible(true);
        $refEntryProp->setValue($step, 45);
        $this->assertEquals(45, $step->getEntryId());
    }

    /**
     * Проверяем установку finishDate
     */
    public function testSetFinishDate()
    {
        $d = new DateTime();
        $step = new SimpleStep(1, 1, 1, 1, 1, $d, $d, $d, 1, [1, 2, 3], 'c');

        $refDateProp = new \ReflectionProperty(SimpleStep::class, 'finishDate');
        $refDateProp->setAccessible(true);

        $d1 = new DateTime('2012-11-12');
        $step->setFinishDate($d1);
        $this->assertEquals($d1, $refDateProp->getValue($step));

        try {
            $step->setFinishDate('incorrect date');
            $this->fail('Не сработала ошибка при установке некорректной даты');
        } catch (\Exception $e) {
        }
    }

    /**
     * Проверяем получение finishDate
     */
    public function testGetFinishDate()
    {
        $d = new DateTime();
        $step = new SimpleStep(1, 1, 1, 1, 1, $d, $d, $d, 1, [1, 2, 3], 'c');
        $refDateProp = new \ReflectionProperty(SimpleStep::class, 'finishDate');
        $refDateProp->setAccessible(true);
        $d1 = new DateTime('2012-11-12');
        $refDateProp->setValue($step, $d1);

        $this->assertEquals($d1, $step->getFinishDate());
    }

    /**
     * @expectedException \OldTown\Workflow\Exception\ArgumentNotNumericException
     */
    public function testSetId()
    {
        $d = new DateTime();
        $step = new SimpleStep(1, 1, 1, 1, 1, $d, $d, $d, 1, [1, 2, 3], 'c');

        $refIdProp = new \ReflectionProperty(SimpleStep::class, 'id');
        $refIdProp->setAccessible(true);

        $step->setId(4);
        $this->assertEquals(4, $refIdProp->getValue($step));

        $step->setId('5a');
        $this->assertEquals(5, $refIdProp->getValue($step));

        // test not int type exception
        $step->setId('something');
    }

    /**
     * Проверяем получение id
     */
    public function testGetId()
    {
        $d = new DateTime();
        $step = new SimpleStep(1, 1, 1, 1, 1, $d, $d, $d, 1, [1, 2, 3], 'c');

        $refIdProp = new \ReflectionProperty(SimpleStep::class, 'id');
        $refIdProp->setAccessible(true);

        $refIdProp->setValue($step, 123);
        $this->assertEquals(123, $step->getId());
    }

    /**
     * Проверяем установку owner
     */
    public function testSetOwner()
    {
        $d = new DateTime();
        $step = new SimpleStep(1, 1, 1, 1, 1, $d, $d, $d, 1, [1, 2, 3], 'c');

        $refOwnerProp = new \ReflectionProperty(SimpleStep::class, 'owner');
        $refOwnerProp->setAccessible(true);

        $step->setOwner('owner');
        $this->assertEquals('owner', $refOwnerProp->getValue($step));
    }

    /**
     * Проверяем получение owner
     */
    public function testGetOwner()
    {
        $d = new DateTime();
        $step = new SimpleStep(1, 1, 1, 1, 1, $d, $d, $d, 1, [1, 2, 3], 'c');

        $refOwnerProp = new \ReflectionProperty(SimpleStep::class, 'owner');
        $refOwnerProp->setAccessible(true);

        $refOwnerProp->setValue($step, 'own3er');
        $this->assertEquals('own3er', $step->getOwner());
    }

    /**
     * Проверяем установку id предыдущих шагов
     */
    public function testSetPreviousStepIds()
    {
        $d = new DateTime();
        $step = new SimpleStep(1, 1, 1, 1, 1, $d, $d, $d, 1, [1, 2, 3], 'c');

        $refProp = new \ReflectionProperty(SimpleStep::class, 'previousStepIds');
        $refProp->setAccessible(true);

        $step->setPreviousStepIds([5, 6, 7]);
        $this->assertEquals([5, 6, 7], $refProp->getValue($step));

        // test type "array" in method hint
        $this->setExpectedException('PHPUnit_Framework_Error');
        $step->setPreviousStepIds(null);
    }

    /**
     * Проверяем получение id предыдущих шагов
     */
    public function testGetPreviousStepIds()
    {
        $d = new DateTime();
        $step = new SimpleStep(1, 1, 1, 1, 1, $d, $d, $d, 1, [1, 2, 3], 'c');

        $refProp = new \ReflectionProperty(SimpleStep::class, 'previousStepIds');
        $refProp->setAccessible(true);
        $refProp->setValue($step, [4, 5, 6]);
        $this->assertEquals([4, 5, 6], $step->getPreviousStepIds());
    }

    /**
     * Проверяем установку startDate
     */
    public function testSetStartDate()
    {
        $d = new DateTime();
        $step = new SimpleStep(1, 1, 1, 1, 1, $d, $d, $d, 1, [1, 2, 3], 'c');

        $refProp = new \ReflectionProperty(SimpleStep::class, 'startDate');
        $refProp->setAccessible(true);

        $d1 = new DateTime('2012-11-12');
        $step->setStartDate($d1);
        $this->assertEquals($d1, $refProp->getValue($step));

        try {
            $step->setStartDate('incorrect date');
            $this->fail('Не сработала ошибка при установке некорректной даты');
        } catch (\Exception $e) {
        }
    }

    /**
     * Проверяем получение startDate
     */
    public function testGetStartDate()
    {
        $d = new DateTime();
        $step = new SimpleStep(1, 1, 1, 1, 1, $d, $d, $d, 1, [1, 2, 3], 'c');

        $refProp = new \ReflectionProperty(SimpleStep::class, 'startDate');
        $refProp->setAccessible(true);
        $d1 = new DateTime('2012-11-12');
        $refProp->setValue($step, $d1);

        $this->assertEquals($d1, $step->getStartDate());
    }

    /**
     * Проверяем установку статуса
     */
    public function testSetStatus()
    {
        $d = new DateTime();
        $step = new SimpleStep(1, 1, 1, 1, 1, $d, $d, $d, 1, [1, 2, 3], 'c');

        $refStatusProp = new \ReflectionProperty(SimpleStep::class, 'status');
        $refStatusProp->setAccessible(true);

        $step->setStatus('somestatus');
        $this->assertEquals('somestatus', $refStatusProp->getValue($step));

        $step->setStatus(null);
        $this->assertEquals('', $refStatusProp->getValue($step));
    }

    /**
     * Проверяем получение статуса
     */
    public function testGetStatus()
    {
        $d = new DateTime();
        $step = new SimpleStep(1, 1, 1, 1, 1, $d, $d, $d, 1, [1, 2, 3], 'c');

        $refStatusProp = new \ReflectionProperty(SimpleStep::class, 'status');
        $refStatusProp->setAccessible(true);
        $refStatusProp->setValue($step, 'somestatus');
        $this->assertEquals('somestatus', $step->getStatus());
    }

    /**
     * @expectedException \OldTown\Workflow\Exception\ArgumentNotNumericException
     */
    public function testSetStepId()
    {
        $d = new DateTime();
        $step = new SimpleStep(1, 1, 1, 1, 1, $d, $d, $d, 1, [1, 2, 3], 'c');

        $refStepIdProp = new \ReflectionProperty(SimpleStep::class, 'stepId');
        $refStepIdProp->setAccessible(true);

        $step->setStepId(4);
        $this->assertEquals(4, $refStepIdProp->getValue($step));

        $step->setStepId('5a');
        $this->assertEquals(5, $refStepIdProp->getValue($step));

        // test invalid int exception
        $step->setId('something');
    }

    /**
     * Проверяем получение id шага
     */
    public function testGetStepId()
    {
        $d = new DateTime();
        $step = new SimpleStep(1, 1, 1, 1, 1, $d, $d, $d, 1, [1, 2, 3], 'c');
        $refStepIdProp = new \ReflectionProperty(SimpleStep::class, 'stepId');
        $refStepIdProp->setAccessible(true);
        $refStepIdProp->setValue($step, 52);
        $this->assertEquals(52, $step->getStepId());
    }

    /**
     * Проверяем преобразование объекта в строку
     */
    public function testToString()
    {
        $d = new DateTime();
        $step = new SimpleStep(1, 2, 3, 4, 'ow', $d, $d, $d, 6, [1, 2, 3], 'c');
        $this->assertEquals(
            'SimpleStep@ 3[owner=ow, actionId=4, status=6]',
            $step->__toString()
        );
    }

    /**
     * Проверяем triggerError для serialize
     */
    public function testSerialize()
    {
        set_error_handler(function () {
            TestCase::assertEquals('Метод OldTown\Workflow\Spi\SimpleStep::serialize класса OldTown\Workflow\Spi\SimpleStep требуется реализовать', func_get_arg(1));
        });
        $d = new DateTime();
        $step = new SimpleStep(1, 2, 3, 4, 'ow', $d, $d, $d, 6, [1, 2, 3], 'c');
        $step->serialize();
    }

    /**
     * Проверяем triggerError для unserialize
     */
    public function testUnserialize()
    {
        set_error_handler(function () {
            TestCase::assertEquals('Метод OldTown\Workflow\Spi\SimpleStep::unserialize класса OldTown\Workflow\Spi\SimpleStep требуется реализовать', func_get_arg(1));
        });
        $d = new DateTime();
        $step = new SimpleStep(1, 2, 3, 4, 'ow', $d, $d, $d, 6, [1, 2, 3], 'c');
        $step->unserialize(null);
    }
}
