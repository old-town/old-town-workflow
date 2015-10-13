<?php
/**
 * @link    https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\PhpUnitTest\Loader;

use InterNations\Component\HttpMock\PHPUnit\HttpMockTrait;
use OldTown\Workflow\Loader\WorkflowDescriptor;
use OldTown\Workflow\PhpUnit\Test\Paths;
use PHPUnit_Framework_TestCase as TestCase;
use OldTown\Workflow\Loader\XmlWorkflowFactory;
use OldTown\Workflow\Loader\XMLWorkflowFactory\WorkflowConfig;
use OldTown\Workflow\PhpUnit\Utils\DirUtilTrait;

/**
 * Class XMLWorkflowFactoryTest
 *
 * @package OldTown\Workflow\PhpUnitTest\Loader
 */
class XMLWorkflowFactoryTest extends TestCase
{
    use HttpMockTrait, DirUtilTrait;

    /**
     * @var XmlWorkflowFactory
     */
    private $xmlWorkflowFactory;

    /**
     * @var mixed
     */
    private $originalDefaultPathsToWorkflows;

    /**
     *
     */
    public static function setUpBeforeClass()
    {
        static::setUpHttpMockBeforeClass('8082', 'localhost');
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
        $this->originalDefaultPathsToWorkflows = XmlWorkflowFactory::getDefaultPathsToWorkflows();
        $this->xmlWorkflowFactory = new XmlWorkflowFactory();
    }

    /**
     *
     */
    public function tearDown()
    {
        XmlWorkflowFactory::setDefaultPathsToWorkflows($this->originalDefaultPathsToWorkflows);
        $this->tearDownHttpMock();
    }



    /**
     * Проверка работы с layout
     *
     * @return void
     */
    public function testSetterGetterLayout()
    {
        $this->xmlWorkflowFactory->setLayout('test', 'test');

        static::assertNull($this->xmlWorkflowFactory->getLayout('example'));
    }


    /**
     * Проверка работы с isModifiable
     *
     * @return void
     */
    public function testIsModifiable()
    {
        static::assertTrue($this->xmlWorkflowFactory->isModifiable('example'));
    }


    /**
     * Проверка работы с getName
     *
     * @return void
     */
    public function testGetName()
    {
        $expected = '';
        $actual = $this->xmlWorkflowFactory->getName();
        static::assertEquals($expected, $actual);
    }


    /**
     * Проверка работы с serialize
     *
     * @return void
     */
    public function testSerialize()
    {
        static::assertNull($this->xmlWorkflowFactory->serialize());
    }


    /**
     * Проверка работы с Unserialize
     *
     * @return void
     */
    public function testUnserialize()
    {

        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $actual = $this->xmlWorkflowFactory->unserialize('example');

        static::assertNull($actual);
    }


    /**
     * Получение имен workflow
     *
     * @expectedException \OldTown\Workflow\Exception\FactoryException
     * @expectedExceptionMessage XmlWorkflowFactory не содержит имена workflow
     *
     * @return void
     */
    public function testGetWorkflowNames()
    {
        $this->xmlWorkflowFactory->getWorkflowNames();
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
        $this->xmlWorkflowFactory->removeWorkflow('example');
    }


    /**
     * Переименовывание workflow
     *
     * @return void
     */
    public function testRenameWorkflow()
    {
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $actual = $this->xmlWorkflowFactory->renameWorkflow('example');

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
        $actual = $this->xmlWorkflowFactory->save();

        static::assertNull($actual);
    }



    /**
     * Проверка создания Workflow
     *
     * @return void
     */
    public function testCreateWorkflow()
    {

        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $actual = $this->xmlWorkflowFactory->createWorkflow('example');

        static::assertNull($actual);
    }

    /**
     *
     *
     */
    public function testInitDone()
    {
        XmlWorkflowFactory::addDefaultPathToWorkflows(Paths::getPathToCommonDataDir());

        $this->xmlWorkflowFactory->getProperties()->setProperty(XmlWorkflowFactory::RESOURCE_PROPERTY, 'workflows.xml');

        $this->xmlWorkflowFactory->initDone();

        $workflow = $this->xmlWorkflowFactory->getWorkflow('example');


        static::assertEquals(true, $workflow instanceof WorkflowDescriptor);
    }

    /**
     * @expectedException \OldTown\Workflow\Exception\InvalidParsingWorkflowException
     *
     */
    public function testInvalidConfig()
    {
        $this->http->mock
            ->when()
            ->methodIs('GET')
            ->pathIs('/foo')
            ->then()
            ->body('invalid content')
            ->end();
        $this->http->setUp();

        $url = 'http://localhost:8082/foo';


        /** @var XmlWorkflowFactory|\PHPUnit_Framework_MockObject_MockObject $xmlWorkflowFactoryMock */
        $xmlWorkflowFactoryMock = $this->getMock(XmlWorkflowFactory::class, [
            'getPathWorkflowFile'
        ]);

        $xmlWorkflowFactoryMock->expects(static::once())->method('getPathWorkflowFile')->will(static::returnValue($url));


        $xmlWorkflowFactoryMock->initDone();
    }


    /**
     * @expectedException \OldTown\Workflow\Exception\FactoryException
     * @expectedExceptionMessage Нет workflow с именем invalid-name
     *
     */
    public function testGetWorkflowInvalidName()
    {
        $this->xmlWorkflowFactory->getWorkflow('invalid-name');
    }



    /**
     * Тестирование кеша при получение дескриптора workflow
     *
     *
     */
    public function testGetWorkflowReload()
    {
        try {
            $testDir = $this->setUpTestDir([
                'workflows.xml',
                'example.xml'
            ], Paths::getPathToCommonDataDir());


            XmlWorkflowFactory::addDefaultPathToWorkflows($testDir);
            $this->xmlWorkflowFactory->getProperties()->setProperty(XmlWorkflowFactory::RESOURCE_PROPERTY, 'workflows.xml');
            $this->xmlWorkflowFactory->getProperties()->setProperty(XmlWorkflowFactory::RELOAD_PROPERTY, 'true');

            $this->xmlWorkflowFactory->initDone();
            $this->xmlWorkflowFactory->getWorkflow('example');

            $pathToTestWorkflow = $testDir . DIRECTORY_SEPARATOR . 'example.xml';

            $dom = new \DOMDocument();
            $dom->load($pathToTestWorkflow);
            $xpath = new \DOMXpath($dom);
            $meta = $xpath->query('//workflow/meta[@name="lastModified"]')->item(0);

            $expected = 'test';
            $meta->nodeValue = $expected;
            sleep(1);
            $dom->save($pathToTestWorkflow);
            clearstatcache();
            $descriptor = $this->xmlWorkflowFactory->getWorkflow('example');
            $metaAttributes = $descriptor->getMetaAttributes();

            static::assertEquals($expected, $metaAttributes['lastModified']);
        } finally {
            $this->tearDownTestDir();
        }
    }

    /**
     * Попытка загрузить некорректный workflow файл
     *
     * @expectedException \OldTown\Workflow\Exception\FactoryException
     * @expectedExceptionMessageRegExp /Некорректный дескрипторв workflow:.*+/
     */
    public function testLoadInvalidWorkflow()
    {
        XmlWorkflowFactory::addDefaultPathToWorkflows(Paths::getPathToInvalidWorkflowDir());

        $this->xmlWorkflowFactory->getProperties()->setProperty(XmlWorkflowFactory::RESOURCE_PROPERTY, 'workflows.xml');

        $this->xmlWorkflowFactory->initDone();

        $workflow = $this->xmlWorkflowFactory->getWorkflow('example');


        static::assertEquals(true, $workflow instanceof WorkflowDescriptor);
    }


    /**
     * Проверка ситуации когда в конфиге workflow не задана атрибут baseDir
     *
     */
    public function testNoBaseDir()
    {
        XmlWorkflowFactory::addDefaultPathToWorkflows(Paths::getPathToInvalidWorkflowConfig());


        /** @var XmlWorkflowFactory|\PHPUnit_Framework_MockObject_MockObject $xmlWorkflowFactory */
        $xmlWorkflowFactory = $this->getMock(XmlWorkflowFactory::class, ['buildWorkflowConfig']);
        /** @var WorkflowConfig $mockConfig */
        $mockConfig = $this->getMock(WorkflowConfig::class, [], [], '', false);
        $mockConfig->url = Paths::getPathToInvalidWorkflowConfig() . DIRECTORY_SEPARATOR . 'example.xml';

        $xmlWorkflowFactory->expects(static::once())
                           ->method('buildWorkflowConfig')
                           ->with(static::isNull(), static::equalTo('file'), static::equalTo('example.xml'))
                           ->will(static::returnValue($mockConfig));


        $xmlWorkflowFactory->getProperties()->setProperty(XmlWorkflowFactory::RESOURCE_PROPERTY, 'workflows-no-base-dir.xml');
        $xmlWorkflowFactory->initDone();

        $xmlWorkflowFactory->getWorkflow('example');
    }


    /**
     * Проверка ситуации когда отсутствует файл с описанием используемых workflow
     *
     * @expectedException \OldTown\Workflow\Exception\FactoryException
     * @expectedExceptionMessage Не удалось найти файл workflow
     */
    public function testNotFoundWorkflowsFile()
    {
        $this->xmlWorkflowFactory->getProperties()->setProperty(XmlWorkflowFactory::RESOURCE_PROPERTY, 'not exists file');

        $this->xmlWorkflowFactory->initDone();
    }


    /**
     * Проверка ситуации когда в конфиге workflow  задана некорректынй атрибут baseDir
     *
     * @expectedException \OldTown\Workflow\Exception\RuntimeException
     * @expectedExceptionMessage Отсутствует ресурс ./invalid-base-dir
     */
    public function testInvalidBaseDir()
    {
        XmlWorkflowFactory::addDefaultPathToWorkflows(Paths::getPathToInvalidWorkflowConfig());
        $this->xmlWorkflowFactory->getProperties()->setProperty(XmlWorkflowFactory::RESOURCE_PROPERTY, 'workflows-invalid-base-dir.xml');

        $this->xmlWorkflowFactory->initDone();
    }


    /**
     * Сохранение workflow
     *
     *
     * @return void
     */
    public function testSaveWorkflow()
    {
        try {
            $testDir = $this->setUpTestDir([
                'workflows.xml',
                'example.xml',
                'new-example.xml'
            ], Paths::getPathToSaveWorkflowDir());


            XmlWorkflowFactory::addDefaultPathToWorkflows($testDir);
            $this->xmlWorkflowFactory->getProperties()->setProperty(XmlWorkflowFactory::RESOURCE_PROPERTY, 'workflows.xml');

            $this->xmlWorkflowFactory->initDone();
            $workflow = $this->xmlWorkflowFactory->getWorkflow('newExample');
            $this->xmlWorkflowFactory->saveWorkflow('example', $workflow, true);

            $path = $testDir . DIRECTORY_SEPARATOR;
            static::assertXmlFileEqualsXmlFile($path . 'new-example.xml', $path . 'example.xml');
        } finally {
            $this->tearDownTestDir();
        }
    }

    /**
     * Сохранение workflow
     *
     * @expectedException \OldTown\Workflow\Exception\InvalidWriteWorkflowException
     * @expectedExceptionMessageRegExp /Ошибка при архивирование оригинального файла workflow .*+/
     *
     * @return void
     */
    public function testSaveWorkflowInvalidBackup()
    {
        try {
            $testDir = $this->setUpTestDir([
                'workflows.xml',
                'example.xml',
                'new-example.xml'
            ], Paths::getPathToSaveWorkflowDir());


            XmlWorkflowFactory::addDefaultPathToWorkflows($testDir);
            /** @var XmlWorkflowFactory|\PHPUnit_Framework_MockObject_MockObject $xmlWorkflowFactory */
            $xmlWorkflowFactory = $this->getMock(XmlWorkflowFactory::class, ['createBackupFile']);
            $xmlWorkflowFactory->expects(static::once())->method('createBackupFile')->will(static::returnValue(false));

            $xmlWorkflowFactory->getProperties()->setProperty(XmlWorkflowFactory::RESOURCE_PROPERTY, 'workflows.xml');

            $xmlWorkflowFactory->initDone();
            $workflow = $xmlWorkflowFactory->getWorkflow('newExample');
            $xmlWorkflowFactory->saveWorkflow('example', $workflow, true);
        } finally {
            $this->tearDownTestDir();
        }
    }


    /**
     * Сохранение workflow
     *
     * @expectedException \OldTown\Workflow\Exception\InvalidWriteWorkflowException
     * @expectedExceptionMessageRegExp /Ошибка при переименовывание нового файла workflow .*+/
     *
     * @return void
     */
    public function testSaveWorkflowInvalidCreateNewFile()
    {
        try {
            $testDir = $this->setUpTestDir([
                'workflows.xml',
                'example.xml',
                'new-example.xml'
            ], Paths::getPathToSaveWorkflowDir());


            XmlWorkflowFactory::addDefaultPathToWorkflows($testDir);
            /** @var XmlWorkflowFactory|\PHPUnit_Framework_MockObject_MockObject $xmlWorkflowFactory */
            $xmlWorkflowFactory = $this->getMock(XmlWorkflowFactory::class, ['createNewWorkflowFile']);
            $xmlWorkflowFactory->expects(static::once())->method('createNewWorkflowFile')->will(static::returnValue(false));

            $xmlWorkflowFactory->getProperties()->setProperty(XmlWorkflowFactory::RESOURCE_PROPERTY, 'workflows.xml');

            $xmlWorkflowFactory->initDone();
            $workflow = $xmlWorkflowFactory->getWorkflow('newExample');
            $xmlWorkflowFactory->saveWorkflow('example', $workflow, true);
        } finally {
            $this->tearDownTestDir();
        }
    }



    /**
     * Сохранение workflow. Указано незарегестрированное имя workflow
     *
     * @expectedException \OldTown\Workflow\Exception\UnsupportedOperationException
     * @expectedExceptionMessage Сохранение workflow не поддерживается
     *
     * @return void
     */
    public function testSaveWorkflowIncorrectName()
    {
        /** @var  WorkflowDescriptor $descriptor */
        $descriptor = $this->getMock(WorkflowDescriptor::class);
        $this->xmlWorkflowFactory->saveWorkflow('example', $descriptor);
    }


    /**
     * Сохранение workflow. Проверка варианта когда указан флаг, не позволяющий перезаписывать существующее workflow
     *
     *
     * @return void
     */
    public function testSaveWorkflowReplace()
    {
        try {
            $testDir = $this->setUpTestDir([
                'workflows.xml',
                'example.xml',
                'new-example.xml'
            ], Paths::getPathToSaveWorkflowDir());


            XmlWorkflowFactory::addDefaultPathToWorkflows($testDir);
            $this->xmlWorkflowFactory->getProperties()->setProperty(XmlWorkflowFactory::RESOURCE_PROPERTY, 'workflows.xml');

            $this->xmlWorkflowFactory->initDone();
            $workflow = $this->xmlWorkflowFactory->getWorkflow('newExample');
            $result = $this->xmlWorkflowFactory->saveWorkflow('example', $workflow, false);

            static::assertEquals(false, $result);
        } finally {
            $this->tearDownTestDir();
        }
    }
}
