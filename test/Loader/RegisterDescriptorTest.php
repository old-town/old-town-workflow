<?php
/**
 * @link    https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Test\Loader;

use PHPUnit_Framework_TestCase as TestCase;
use OldTown\Workflow\Loader\RegisterDescriptor;

/**
 * Class ValidatorDescriptorTest
 *
 * @package OldTown\Workflow\Test\Loader
 */
class RegisterDescriptorTest extends TestCase implements DescriptorTestInterface
{
    use DescriptorTestTrait, ProviderXmlDataTrait, TestAttributeTrait, ArgumentsTraitTest;

    /**
     * Класс тестируемого дескриптора
     *
     * @var string
     */
    const DESCRIPTOR_CLASS_NAME = RegisterDescriptor::class;

    /**
     * Данные для тестирования на чтение аргументов
     *
     * @var array|null
     */
    protected $dataForReadXmlArgTest;

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
            'fileName' => 'register.xml',
            'xpathPattern' => '/register',
            'attributes' => [
                'type' => [
                    'descriptorMethod' => 'getType',
                    'xmlAttributeName' => 'type',
                    'required' => true
                ],
                'id' => [
                    'descriptorMethod' => 'getId',
                    'xmlAttributeName' => 'id',
                    'required' => false
                ],
                'name' => [
                    'descriptorMethod' => 'getVariableName',
                    'xmlAttributeName' => 'variable-name',
                    'required' => true
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
             * Вариант когда отсутствует атрибут type
             */
            'fileName' => 'register-not-exists-type-attribute.xml',
            'xpathPattern' => '/register'
        ],
        [
            /**
             * Вариант когда отсутствует атрибут
             */
            'fileName' => 'register-not-exists-variable-name-attribute.xml',
            'xpathPattern' => '/register'
        ]
    ];

    /**
     * Конфиг для тестирования корректности сохранения элемента в xml
     *
     * @var array
     */
    protected $saveAttributeTestConfig;

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
                'class' => RegisterDescriptor::class,
                'setter' => 'setType',
                'getter' => 'getType',
                'xpathElement' => '/register',
                'attributeName' => 'type',
                'value' => 'testType',
                'di' => function (RegisterDescriptor $descriptor) {
                    $descriptor->setVariableName('testVariableName');

                }
            ],
            [
                'class' => RegisterDescriptor::class,
                'setter' => 'setVariableName',
                'getter' => 'getVariableName',
                'xpathElement' => '/register',
                'attributeName' => 'variable-name',
                'value' => 'testVariableName',
                'di' => function (RegisterDescriptor $descriptor) {
                    $descriptor->setType('testType');
                }
            ],
            [
                'class' => RegisterDescriptor::class,
                'setter' => 'setId',
                'getter' => 'getId',
                'xpathElement' => '/register',
                'attributeName' => 'id',
                'value' => 'testId',
                'di' => function (RegisterDescriptor $descriptor) {
                    $descriptor->setType('testType');
                    $descriptor->setVariableName('testVariableName');
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
        $this->pathToXmlFile = __DIR__ . '/../data/workflow-descriptor/register-descriptor';
    }

    /**
     * Создание дескриптора функции без Dom элемента
     *
     * @return void
     */
    public function testCreateValidatorDescriptorWithoutElement()
    {
        $descriptor = new RegisterDescriptor();

        static::assertInstanceOf(RegisterDescriptor::class, $descriptor);
    }

    /**
     * Тестируем атрибуты элемента
     *
     * @dataProvider testAttributesData
     *
     * @param string $fileName
     * @param string $xpathPattern
     * @param array $attributes
     */
    public function testAttributeValidatorDescriptor($fileName, $xpathPattern, array $attributes = [])
    {
        $this->helperTestAttributeDescriptor(RegisterDescriptor::class, $fileName, $xpathPattern, $attributes);
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
    public function testRequiredAttributeValidatorDescriptor($fileName, $xpathPattern)
    {
        /** @var \DOMElement $testNode */
        $testNode = $this->getTestNode($fileName, $xpathPattern);
        new RegisterDescriptor($testNode);
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
     * @param RegisterDescriptor $descriptor
     *
     * @return void
     */
    public function defaultDiDescriptor(RegisterDescriptor $descriptor)
    {
        $descriptor->setType('defaultType');
        $descriptor->setVariableName('testVariableName');
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
                'file' => 'register-args.xml',
                'xpathRoot' => '/register',
            ]
        ];

        return $this->dataForReadXmlArgTest;
    }
}
