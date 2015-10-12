<?php
/**
 * @link    https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\PhpUnitTest\Loader\XMLWorkflowFactory;

use OldTown\Workflow\Loader\XMLWorkflowFactory\WorkflowConfig;
use PHPUnit_Framework_TestCase as TestCase;


/**
 * Class WorkflowConfigTest
 *
 * @package OldTown\Workflow\PhpUnitTest\Loader
 */
class WorkflowConfigTest extends TestCase
{
    /**
     * @var WorkflowConfig
     */
    private $workflowConfig;

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->workflowConfig = $this->getMockBuilder(WorkflowConfig::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @expectedException        \BadMethodCallException
     * @expectedExceptionMessage Работа с Url. Необходимо портировать из оригинального проекта
     */
    public function testInitTypeUrlExceptionMessage()
    {
        new WorkflowConfig(null, 'URL', null);
    }

    /**
     * Тестируем установку корректных св-тв при передаче корректного файла
     */
    public function testCorrectSetFileType()
    {
        $baseDir = dirname(dirname(__DIR__))  . DIRECTORY_SEPARATOR . 'data';
        $workflowConfig = new WorkflowConfig($baseDir, 'file', 'example.xml');

        $this->assertEquals('example.xml', $workflowConfig->location);
        $this->assertTrue(file_exists($workflowConfig->url));
        $this->assertGreaterThan(0, $workflowConfig->lastModified);
        $this->assertEquals('file', $workflowConfig->type);
    }
}
