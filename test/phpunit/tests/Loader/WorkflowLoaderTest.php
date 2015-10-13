<?php
/**
 * @link    https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\PhpUnitTest\Loader;

use OldTown\Workflow\Loader\WorkflowDescriptor;
use PHPUnit_Framework_TestCase as TestCase;
use OldTown\Workflow\Loader\WorkflowLoader;
use Psr\Http\Message\UriInterface;
use \InterNations\Component\HttpMock\PHPUnit\HttpMockTrait;

/**
 * Class WorkflowLoaderTest
 *
 * @package OldTown\Workflow\PhpUnitTest\Loader
 */
class WorkflowLoaderTest extends TestCase
{
    use HttpMockTrait;

    /**
     * @var string
     */
    private static $exampleWorkflowXml;

    /**
     * Путь до файла с тестовым workflow
     *
     * @var string
     */
    private static $pathToExampleWorkflowXml;

    /**
     *
     */
    public static function setUpBeforeClass()
    {
        static::setUpHttpMockBeforeClass('8082', 'localhost');
        if (!static::$pathToExampleWorkflowXml) {
            $path = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'example.xml';
            static::$pathToExampleWorkflowXml = $path;
        }

        if (!static::$exampleWorkflowXml) {
            static::$exampleWorkflowXml = file_get_contents(static::$pathToExampleWorkflowXml);
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
    public function setUp()
    {
        $this->setUpHttpMock();
    }

    /**
     *
     */
    public function tearDown()
    {
        $this->tearDownHttpMock();
    }

    /**
     * @var string
     */
    protected static $uriClassName = '\Zend\Diactoros\Uri';

    /**
     * @return string
     */
    public static function getUriClassName()
    {
        return self::$uriClassName;
    }

    /**
     * @param $uri
     * @return UriInterface
     */
    protected function uriFactory($uri)
    {
        $uriClassName = self::getUriClassName();

        $uri = new $uriClassName($uri);

        return $uri;
    }

    /**
     *
     */
    public function testLoadFromUrl()
    {
        $this->http->mock
            ->when()
            ->methodIs('GET')
            ->pathIs('/foo')
            ->then()
            ->body(static::$exampleWorkflowXml)
            ->end();
        $this->http->setUp();

        $url = 'http://localhost:8082/foo';
        $uri = self::uriFactory($url);
        $descriptor = WorkflowLoader::load($uri);

        static::assertInstanceOf(WorkflowDescriptor::class, $descriptor);
    }

    /**
     * @expectedException \OldTown\Workflow\Exception\InvalidParsingWorkflowException
     * @expectedExceptionMessage По пути invalid-path отсутствует файл с workflow
     */
    public function testLoadByInvalidPath()
    {
        $path = 'invalid-path';
        WorkflowLoader::load($path);
    }

    /**
     *
     */
    public function testLoadByValidPath()
    {
        $descriptor = WorkflowLoader::load(static::$pathToExampleWorkflowXml);

        static::assertInstanceOf(WorkflowDescriptor::class, $descriptor);
    }

    /**
     * @expectedException \OldTown\Workflow\Exception\InvalidParsingWorkflowException
     * @expectedExceptionMessage Не удалось получить workflow
     */
    public function testLoadByInvalidResource()
    {
        WorkflowLoader::load([]);
    }

    /**
     * @expectedException \OldTown\Workflow\Exception\InvalidParsingWorkflowException
     * @expectedExceptionMessage Ошибка при загрузке workflow из ресурса http://localhost:8082/foo.
     */
    public function testLoadInvalidXml()
    {
        $this->http->mock
            ->when()
            ->methodIs('GET')
            ->pathIs('/foo')
            ->then()
            ->body('invalid-xml')
            ->end();
        $this->http->setUp();

        $url = 'http://localhost:8082/foo';
        $uri = self::uriFactory($url);
        WorkflowLoader::load($uri);
    }
}
