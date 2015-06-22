<?php
/**
 * @link    https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Test\Loader;

use OldTown\Workflow\Exception\InvalidWorkflowDescriptorException;
use PHPUnit_Framework_TestCase as TestCase;
use OldTown\Workflow\Loader\ValidationHelper;
use OldTown\Workflow\Loader\ValidateDescriptorInterface;

/**
 * Class ValidatorDescriptorTest
 * @package OldTown\Workflow\Test\Loader
 */
class ValidationHelperTest extends TestCase
{

    /**
     * Тест когда один из элементов коллекции не прошел валидацию
     *
     * @expectedException \OldTown\Workflow\Exception\InvalidWorkflowDescriptorException
     *
     */
    public function testNotValidCollection()
    {
        $descriptor = $this->getMockForAbstractClass(ValidateDescriptorInterface::class);
        $exception = new InvalidWorkflowDescriptorException();
        $descriptor->expects(static::once())->method('validate')->will(static::throwException($exception));
        $c = [
            $descriptor
        ];

        ValidationHelper::validate($c);

    }

    /**
     * Тест когда коллекция валидна
     *
     */
    public function testValidatorCollection()
    {
        $descriptor = $this->getMockForAbstractClass(ValidateDescriptorInterface::class);
        $c = [
            $descriptor
        ];

        ValidationHelper::validate($c);
    }


    /**
     * Тест пустой коллекции
     *
     */
    public function testEmptyCollection()
    {
        $c = [];
        ValidationHelper::validate($c);
    }

    /**
     * Тест когда один из элементов коллекции не прошел валидацию
     *
     * @expectedException \OldTown\Workflow\Exception\InvalidWorkflowDescriptorException
     *
     */
    public function testNotValidTraversableCollection()
    {
        $descriptor = $this->getMockForAbstractClass(ValidateDescriptorInterface::class);
        $exception = new InvalidWorkflowDescriptorException();
        $descriptor->expects(static::once())->method('validate')->will(static::throwException($exception));
        $c = new \ArrayObject([
            $descriptor
        ]);

        ValidationHelper::validate($c);

    }
}
