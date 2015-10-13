<?php
/**
 * @link    https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\PhpUnitTest\Loader;

use InterNations\Component\HttpMock\PHPUnit\HttpMockTrait;
use OldTown\Workflow\Loader\WorkflowDescriptor;
use PHPUnit_Framework_TestCase as TestCase;
use OldTown\Workflow\Loader\UrlWorkflowFactory;


/**
 * Class UrlWorkflowFactoryTest
 *
 * @package OldTown\Workflow\PhpUnitTest\Loader
 */
class UrlWorkflowFactoryTest extends TestCase
{
    use HttpMockTrait;

    /**
     * @var UrlWorkflowFactory
     */
    private $urlWorkflowFactory;

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
        $this->urlWorkflowFactory = new UrlWorkflowFactory();
    }

    /**
     *
     */
    public function tearDown()
    {
        $this->tearDownHttpMock();
    }

    /**
     * Проверка работы с layout
     *
     * @return void
     */
    public function testSetterGetterLayout()
    {
        $this->urlWorkflowFactory->setLayout('test', 'test');

        static::assertNull($this->urlWorkflowFactory->getLayout('example'));
    }


    /**
     * Проверка работы с isModifiable
     *
     * @return void
     */
    public function testIsModifiable()
    {
        static::assertFalse($this->urlWorkflowFactory->isModifiable('example'));
    }


    /**
     * Проверка работы с getName
     *
     * @return void
     */
    public function testGetName()
    {
        $expected = '';
        $actual = $this->urlWorkflowFactory->getName();
        static::assertEquals($expected, $actual);
    }


    /**
     * Проверка работы с serialize
     *
     * @return void
     */
    public function testSerialize()
    {
        static::assertNull($this->urlWorkflowFactory->serialize());
    }


    /**
     * Проверка работы с Unserialize
     *
     * @return void
     */
    public function testUnserialize()
    {

        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $actual = $this->urlWorkflowFactory->unserialize('example');

        static::assertNull($actual);
    }


    /**
     * Получение имен workflow
     *
     * @expectedException \OldTown\Workflow\Exception\FactoryException
     * @expectedExceptionMessage URLWorkflowFactory не содержит имена workflow
     *
     * @return void
     */
    public function testGetWorkflowNames()
    {
        $this->urlWorkflowFactory->getWorkflowNames();
    }


    /**
     * Проверка создания Workflow
     *
     * @return void
     */
    public function testCreateWorkflow()
    {

        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $actual = $this->urlWorkflowFactory->createWorkflow('example');

        static::assertNull($actual);
    }


    /**
     * Удаление workflow
     *
     * @expectedException \OldTown\Workflow\Exception\FactoryException
     * @expectedExceptionMessage Удаление workflow не поддерживается
     *
     * @return void
     */
    public function testRemoveWorkflow()
    {
        $this->urlWorkflowFactory->removeWorkflow('example');
    }


    /**
     * Переименовывание workflow
     *
     * @return void
     */
    public function testRenameWorkflow()
    {
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $actual = $this->urlWorkflowFactory->renameWorkflow('example');

        static::assertNull($actual);
    }


    /**
     * Сохранение
     *
     *
     * @return void
     */
    public function testSave()
    {
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $actual = $this->urlWorkflowFactory->save();

        static::assertNull($actual);
    }


    /**
     * Сохранение workflow
     *
     *
     * @return void
     */
    public function testWorkflow()
    {
        /** @var WorkflowDescriptor $workflowDescriptor */
        $workflowDescriptor = $this->getMock(WorkflowDescriptor::class);
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $actual = $this->urlWorkflowFactory->saveWorkflow('example', $workflowDescriptor, false);

        static::assertNull($actual);
    }


    /**
     * Сохранение workflow
     *
     *
     * @return void
     */
    public function testGetterSetterUriClassName()
    {
        $current = UrlWorkflowFactory::getUriClassName();

        $expected = 'test';
        UrlWorkflowFactory::setUriClassName($expected);
        $actual = UrlWorkflowFactory::getUriClassName();
        UrlWorkflowFactory::setUriClassName($current);

        static::assertEquals($expected, $actual);
    }

    /**
     * Тест получения workflow по url
     *
     * @return void
     */
    public function testGetWorkflow()
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

        $descriptor = $this->urlWorkflowFactory->getWorkflow($url);

        static::assertInstanceOf(WorkflowDescriptor::class, $descriptor);
    }


    /**
     * Тест получения workflow по url
     *
     * @return void
     */
    public function testGetWorkflowFromCache()
    {
        $this->urlWorkflowFactory->getProperties()->setProperty(UrlWorkflowFactory::CACHE, 'true');

        $this->http->mock
            ->when()
            ->methodIs('GET')
            ->pathIs('/foo')
            ->then()
            ->body(static::$exampleWorkflowXml)
            ->end();
        $this->http->setUp();

        $url = 'http://localhost:8082/foo';
        //save cache
        $expectedDescriptor = $this->urlWorkflowFactory->getWorkflow($url);
        //read cache
        $actualDescriptor = $this->urlWorkflowFactory->getWorkflow($url);


        static::assertTrue($expectedDescriptor === $actualDescriptor);
    }


    /**
     * Тест получения workflow по url. Отдается невалидный xml
     *
     * @expectedException \OldTown\Workflow\Exception\FactoryException
     * @expectedExceptionMessage Ошибка при загрузке workflow: http://localhost:8082/foo
     * @return void
     */
    public function testGetWorkflowFromInvalidXml()
    {
        $this->http->mock
            ->when()
            ->methodIs('GET')
            ->pathIs('/foo')
            ->then()
            ->body('invalid xml')
            ->end();
        $this->http->setUp();

        $url = 'http://localhost:8082/foo';

        $this->urlWorkflowFactory->getWorkflow($url);
    }
}
