<?php
/**
 * @link    https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Test\Loader;

use PHPUnit_Framework_TestCase as TestCase;
use OldTown\Workflow\Loader\FunctionDescriptor;

/**
 * Class FunctionDescriptorTest
 *
 * @package OldTown\Workflow\Test\Loader
 */
class FunctionDescriptorTest extends TestCase implements DescriptorTestInterface
{
    use DescriptorTestTrait, ProviderXmlDataTrait, TestAttributeTrait, ArgumentsTraitTest;

    /**
     * Класс тестируемого дескриптора
     *
     * @var string
     */
    const DESCRIPTOR_CLASS_NAME = FunctionDescriptor::class;

    /**
     * Тестируем атрибуты
     *
     * @var array
     */
    protected $testAttributesConfig = [
        [
            /**
             * Вариант когда присутствуют все атрибуты
             */
            'fileName'     => 'function.xml',
            'xpathPattern' => '/function',
            'attributes'   => [
                'type' => [
                    'descriptorMethod' => 'getType',
                    'xmlAttributeName' => 'type',
                    'required'         => true
                ],
                'id'   => [
                    'descriptorMethod' => 'getId',
                    'xmlAttributeName' => 'id',
                    'required'         => false
                ],
                'name' => [
                    'descriptorMethod' => 'getName',
                    'xmlAttributeName' => 'name',
                    'required'         => false
                ]
            ]
        ]
    ];

    /**
     * Тестируем обязательные атрибуты
     *
     * @var array
     */
    protected $testRequiredAttributesConfig = [
        [
            /**
             * Вариант когда присутствуют все атрибуты
             */
            'fileName'     => 'function-not-exists-type-attribute.xml',
            'xpathPattern' => '/function'
        ]
    ];

    /**
     * Конфиг для тестирования корректности сохранения элемента в xml
     *
     * @var array
     */
    protected $saveAttributeTestConfig;

    /**
     * Данные для тестирования на чтение аргументов
     *
     * @var array|null
     */
    protected $dataForReadXmlArgTest;

    /**
     * @return array
     */
    public function saveAttributeTestData()
    {
        if ($this->saveAttributeTestConfig) {
            return $this->saveAttributeTestConfig;
        }
        $this->saveAttributeTestConfig = [
            [
                'class'         => FunctionDescriptor::class,
                'setter'        => 'setType',
                'getter'        => 'getType',
                'xpathElement'  => '/function',
                'attributeName' => 'type',
                'value'         => 'testType'
            ],
            [
                'class'         => FunctionDescriptor::class,
                'setter'        => 'setName',
                'getter'        => 'getName',
                'xpathElement'  => '/function',
                'attributeName' => 'name',
                'value'         => 'testName',
                'di' => function(FunctionDescriptor $descriptor) {
                    $descriptor->setType('testType');
                }
            ],
            [
                'class'         => FunctionDescriptor::class,
                'setter'        => 'setId',
                'getter'        => 'getId',
                'xpathElement'  => '/function',
                'attributeName' => 'id',
                'value'         => 'testId',
                'di' => function(FunctionDescriptor $descriptor) {
                    $descriptor->setType('testType');
                }
            ]
        ];
        return $this->saveAttributeTestConfig;
    }


    /**
     * @return array
     */
    public function testAttributesData()
    {
        return $this->testAttributesConfig;
    }

    /**
     * @return array
     */
    public function testRequiredAttributesData()
    {
        return $this->testRequiredAttributesConfig;
    }

    /**
     * Настраиваем тестовое окружение
     */
    public function setUp()
    {
        $this->pathToXmlFile = __DIR__ . '/../data/workflow-descriptor/function-descriptor';
    }

    /**
     * Создание дескриптора функции без Dom элемента
     *
     * @return void
     */
    public function testCreateFunctionDescriptorWithoutElement()
    {
        $descriptor = new FunctionDescriptor();

        static::assertInstanceOf('\OldTown\Workflow\Loader\FunctionDescriptor', $descriptor);
    }

    /**
     * Тестируем атрибуты элемента
     *
     * @dataProvider testAttributesData
     *
     * @param string $fileName
     * @param string $xpathPattern
     * @param array  $attributes
     */
    public function testAttributeFunctionDescriptor($fileName, $xpathPattern, array $attributes = [])
    {
        $this->helperTestAttributeDescriptor(FunctionDescriptor::class, $fileName, $xpathPattern, $attributes);
    }

    /**
     * Тестируем наличие обязательных атрибутов
     *
     * @dataProvider testRequiredAttributesData
     *
     * @expectedException \OldTown\Workflow\Exception\NotExistsRequiredAttributeException
     *
     * @param string $fileName
     * @param string $xpathPattern
     */
    public function testRequiredAttributeFunctionDescriptor($fileName, $xpathPattern)
    {
        /** @var \DOMElement $testNode */
        $testNode = $this->getTestNode($fileName, $xpathPattern);
        new FunctionDescriptor($testNode);
    }


    /**
     * Создание дескриптора функции без Dom элемента
     *
     * @dataProvider saveAttributeTestData
     *
     * @param string $class - имя класса дескриптора элемента workflow
     * @param string $setter - имя метода устанавливающего  значение атрибута из дескриптора
     * @param string $getter - имя метода возвращающего  значение атрибута из дескриптора
     * @param string $xpathElement - xpath выражение позволяющие получить тестируемый элемент в сгенерированном xml
     * @param $attributeName - имя атрибута в сгенерированном xml
     * @param $value - значение атрибута
     *
     * @param callable $di
     */
    public function testWriteXml($class, $setter, $getter, $xpathElement, $attributeName, $value, callable $di = null)
    {
        $this->saveAttributeTest($class, $setter, $getter, $xpathElement, $attributeName, $value, $di);
    }

    /**
     * Настройка зависимостей для тестирования атрибутов
     *
     * @param FunctionDescriptor $descriptor
     *
     * @return void
     */
    public function defaultDiDescriptor(FunctionDescriptor $descriptor)
    {
        $descriptor->setType('defaultType');
    }

    /**
     * Возвращает данные для тестирования аргументов
     *
     * @return array
     */
    public function readXmlArgCustomTestData()
    {
        if ($this->dataForReadXmlArgTest) {
            return $this->dataForReadXmlArgTest;
        }

        $this->dataForReadXmlArgTest = [
            [
                'file' => 'function-args.xml',
                'xpathRoot' => '/function',
            ]
        ];

        return $this->dataForReadXmlArgTest;
    }
}
