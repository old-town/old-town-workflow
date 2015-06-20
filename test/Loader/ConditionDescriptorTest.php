<?php
/**
 * @link    https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Test\Loader;

use PHPUnit_Framework_TestCase as TestCase;
use OldTown\Workflow\Loader\ConditionDescriptor;

/**
 * Class ConditionDescriptorTest
 *
 * @package OldTown\Workflow\Test\Loader
 */
class ConditionDescriptorTest extends TestCase implements DescriptorTestInterface
{
    use DescriptorTestTrait, ProviderXmlDataTrait, TestAttributeTrait, ArgumentsTraitTest;

    /**
     * Класс тестируемого дескриптора
     *
     * @var string
     */
    const DESCRIPTOR_CLASS_NAME = ConditionDescriptor::class;

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
            'fileName' => 'condition.xml',
            'xpathPattern' => '/condition',
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
                    'descriptorMethod' => 'getName',
                    'xmlAttributeName' => 'name',
                    'required' => false
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
            'fileName' => 'condition-not-exists-type-attribute.xml',
            'xpathPattern' => '/condition'
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
                'class' => ConditionDescriptor::class,
                'setter' => 'setType',
                'getter' => 'getType',
                'xpathElement' => '/condition',
                'attributeName' => 'type',
                'value' => 'testType',
            ],
            [
                'class' => ConditionDescriptor::class,
                'setter' => 'setName',
                'getter' => 'getName',
                'xpathElement' => '/condition',
                'attributeName' => 'name',
                'value' => 'testName',
                'di' => function (ConditionDescriptor $descriptor) {
                    $descriptor->setType('testType');
                }
            ],
            [
                'class' => ConditionDescriptor::class,
                'setter' => 'setId',
                'getter' => 'getId',
                'xpathElement' => '/condition',
                'attributeName' => 'id',
                'value' => 'testId',
                'di' => function (ConditionDescriptor $descriptor) {
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
        $this->pathToXmlFile = __DIR__ . '/../data/workflow-descriptor/condition-descriptor';
    }

    /**
     * Создание дескриптора функции без Dom элемента
     *
     * @return void
     */
    public function testCreateValidatorDescriptorWithoutElement()
    {
        $descriptor = new ConditionDescriptor();

        static::assertInstanceOf(ConditionDescriptor::class, $descriptor);
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
        $this->helperTestAttributeDescriptor(ConditionDescriptor::class, $fileName, $xpathPattern, $attributes);
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
        new ConditionDescriptor($testNode);
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
     * @param ConditionDescriptor $descriptor
     *
     * @return void
     */
    public function defaultDiDescriptor(ConditionDescriptor $descriptor)
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
                'file' => 'condition-args.xml',
                'xpathRoot' => '/condition',
            ]
        ];

        return $this->dataForReadXmlArgTest;
    }

    /**
     * Проверка чтения атрибута negate - yes
     *
     */
    public function testYesNegateReadAttribute()
    {
        $conditionElement = $this->getTestNode('condition-negate-attribute-yes.xml', '/condition');

        $conditionDescriptor = new ConditionDescriptor($conditionElement);

        $msg = 'Некорректно установлен атрибут negate';
        static::assertTrue($conditionDescriptor->isNegate($msg));
    }


    /**
     * Проверка чтения атрибута negate - значение true
     *
     */
    public function testTrueNegateReadAttribute()
    {
        $conditionElement = $this->getTestNode('condition-negate-attribute-true.xml', '/condition');

        $conditionDescriptor = new ConditionDescriptor($conditionElement);

        $msg = 'Некорректно установлен атрибут negate';
        static::assertTrue($conditionDescriptor->isNegate($msg));
    }


    /**
     * Проверка чтения атрибута negate - значение true
     *
     */
    public function testBadNegateReadAttribute()
    {
        $conditionElement = $this->getTestNode('condition-negate-attribute-error.xml', '/condition');

        $conditionDescriptor = new ConditionDescriptor($conditionElement);

        $msg = 'Некорректно установлен атрибут negate';
        static::assertFalse($conditionDescriptor->isNegate($msg));
    }


    /**
     * Проверка чтения атрибута negate - атрибут не существует
     *
     */
    public function testNotExistsNegateAttribute()
    {
        $conditionElement = $this->getTestNode('condition-negate-attribute-not-exists.xml', '/condition');

        $conditionDescriptor = new ConditionDescriptor($conditionElement);

        $msg = 'Некорректно установлен атрибут negate';
        static::assertFalse($conditionDescriptor->isNegate($msg));
    }


    /**
     * Проверка корректности записи атрибута negate
     *
     */
    public function testWriteNegateAttribute()
    {
        $descriptor = new ConditionDescriptor();
        $descriptor->setType('testType');
        $descriptor->setNegate(true);

        $domDescriptor = new \DOMDocument();
        $domElementDescriptor = $descriptor->writeXml($domDescriptor);

        $baseXml = $domDescriptor->saveXML($domElementDescriptor);

        $testDom = new \DOMDocument();
        $testDom->loadXML($baseXml);

        $testElements = $testDom->getElementsByTagName('condition');

        static::assertEquals(1, $testElements->length, 'Не найден элемент condition');

        /** @var \DOMElement $testElement */
        $testElement = $testElements->item(0);

        static::assertTrue($testElement->hasAttribute('negate'), 'Не найден атрибут negate');

        static::assertEquals('true', $testElement->getAttribute('negate'), 'Неверное значение атрибута negate');
    }


    /**
     * Проверка корректности записи атрибута negate - в случае если значение аналогичного свойствва у дескриптора - false
     *
     */
    public function testWriteFalseNegateAttribute()
    {
        $descriptor = new ConditionDescriptor();
        $descriptor->setType('testType');
        $descriptor->setNegate(false);

        $domDescriptor = new \DOMDocument();
        $domElementDescriptor = $descriptor->writeXml($domDescriptor);

        $baseXml = $domDescriptor->saveXML($domElementDescriptor);

        $testDom = new \DOMDocument();
        $testDom->loadXML($baseXml);

        $testElements = $testDom->getElementsByTagName('condition');

        static::assertEquals(1, $testElements->length, 'Не найден элемент condition');

        /** @var \DOMElement $testElement */
        $testElement = $testElements->item(0);

        static::assertFalse($testElement->hasAttribute('negate'), 'Атрибут не должен быть сгенерирован');
    }
}
