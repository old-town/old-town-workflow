<?php
/**
 * @link    https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\PhpUnitTest\Loader\XMLWorkflowFactory;

use InterNations\Component\HttpMock\PHPUnit\HttpMockTrait;
use OldTown\Workflow\Loader\XMLWorkflowFactory\WorkflowConfig;
use PHPUnit_Framework_TestCase as TestCase;
use OldTown\Workflow\PhpUnit\Test\Paths;
use Zend\Diactoros\Uri;

/**
 * Class WorkflowConfigTest
 *
 * @package OldTown\Workflow\PhpUnitTest\Loader
 */
class WorkflowConfigTest extends TestCase
{
    use HttpMockTrait;

    /**
     * @var WorkflowConfig
     */
    private $workflowConfig;


    /**
     * @var string
     */
    private static $exampleWorkflowConfig;

    /**
     * Путь до файла с тестовым workflow
     *
     * @var string
     */
    private static $pathToExampleWorkflowConfig;

    /**
     * @return void
     */
    public static function setUpBeforeClass()
    {
        static::setUpHttpMockBeforeClass('8082', 'localhost');
        if (!static::$pathToExampleWorkflowConfig) {
            $path = Paths::getPathToDataDir() . DIRECTORY_SEPARATOR . 'osworkflow.xml';
            static::$pathToExampleWorkflowConfig = $path;
        }

        if (!static::$exampleWorkflowConfig) {
            static::$exampleWorkflowConfig = file_get_contents(static::$pathToExampleWorkflowConfig);
        }
    }

    /**
     *
     */
    public static function tearDownAfterClass()
    {
        static::tearDownHttpMockAfterClass();
    }

    /**
     *
     */
    public function tearDown()
    {
        $this->tearDownHttpMock();
    }

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->setUpHttpMock();

        $this->workflowConfig = $this->getMockBuilder(WorkflowConfig::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Тестируем установку корректных св-тв при передаче корректного файла
     */
    public function testCorrectSetFileType()
    {
        $workflowConfig = new WorkflowConfig(Paths::getPathToDataDir(), 'file', 'example.xml');

        static::assertEquals('example.xml', $workflowConfig->location);
        static::assertTrue(file_exists($workflowConfig->url));
        static::assertGreaterThan(0, $workflowConfig->lastModified);
        static::assertEquals('file', $workflowConfig->type);
    }


    /**
     * Тестируем установку корректных св-тв при передаче корректного урл
     */
    public function testCorrectSetUrlType()
    {
        $expectedTime = time();

        $lastModified = new \DateTime();
        $lastModified->setTimestamp($expectedTime);
        $lastModifiedStr = $lastModified->format("D, d M Y H:i:s \G\M\T");


        $this->http->mock
            ->when()
            ->methodIs('GET')
            ->pathIs('/foo')
            ->then()
            ->body(static::$exampleWorkflowConfig)
            ->header('Last-Modified', $lastModifiedStr)
            ->end();
        $this->http->setUp();

        $url = 'http://localhost:8082/foo';

        $workflowConfig = new WorkflowConfig(null, WorkflowConfig::URL_TYPE, $url);

        static::assertEquals($expectedTime, $workflowConfig->lastModified);
        static::assertEquals(WorkflowConfig::URL_TYPE, $workflowConfig->type);
        static::assertEquals($url, $workflowConfig->location);
        static::assertEquals(true, $workflowConfig->url instanceof Uri);
    }


    /**
     * Тестируем установку корректных св-тв в случае если не указан тип ресурса
     */
    public function testCorrectSetDefaultType()
    {
        $path = Paths::getPathToDataDir() . DIRECTORY_SEPARATOR . 'osworkflow.xml';
        $workflowConfig = new WorkflowConfig(null, null, $path);

        static::assertEquals(filemtime($path), $workflowConfig->lastModified);
        static::assertEquals(null, $workflowConfig->type);
        static::assertEquals($path, $workflowConfig->location);
        static::assertEquals(realpath($path), $workflowConfig->url);
    }

    /**
     * Тестируем установку имени класса реализующего обертку для uri
     */
    public function testGetterSetterUriClassName()
    {
        $original = WorkflowConfig::getUriClassName();
        $expected = 'test';
        WorkflowConfig::setUriClassName($expected);
        $actual = WorkflowConfig::getUriClassName();
        WorkflowConfig::setUriClassName($original);

        static::assertEquals($expected, $actual);
    }
}
