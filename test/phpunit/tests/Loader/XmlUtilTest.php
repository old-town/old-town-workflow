<?php
/**
 * @link    https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\PhpUnitTest\Loader;

use OldTown\Workflow\PhpUnit\Test\Paths;
use PHPUnit_Framework_TestCase as TestCase;
use OldTown\Workflow\Loader\XmlUtil;


/**
 * Class XmlUtilTest
 *
 * @package OldTown\Workflow\PhpUnitTest\Loader
 */
class XmlUtilTest extends TestCase
{
    /**
     * @var \DOMXPath
     */
    protected static $xpath;

    /**
     *
     * @return void
     */
    public static function setUpBeforeClass()
    {
        if (null !== static::$xpath) {
            return;
        }

        $path = Paths::getXmlUtilTest() . DIRECTORY_SEPARATOR . 'test.xml';


        $doc = new \DOMDocument();
        $doc->load($path);


        static::$xpath = new \DOMXPath($doc);
    }

    /**
     * Проверка корректного поиска элементов
     */
    public function testGetChildElement()
    {
        /** @var \DOMElement  $elem */
        $elem = static::$xpath->query('//testGetChildElement')->item(0);
        $elem = XmlUtil::getChildElement($elem, 'element1');

        static::assertEquals(\DOMElement::class, get_class($elem));

        static::assertEquals(true, $elem->hasAttribute('level'));
        static::assertEquals(1, $elem->getAttribute('level'));

        static::assertEquals(null, XmlUtil::getChildElement($elem, 'undefined-element'));
    }


    /**
     * Проверка функционала поиска обязательного элемента
     */
    public function testGetRequiredChildElement()
    {
        /** @var \DOMElement  $elem */
        $elem = static::$xpath->query('//testGetRequiredChildElement')->item(0);
        $elem = XmlUtil::getRequiredChildElement($elem, 'element1');

        static::assertEquals(\DOMElement::class, get_class($elem));

        static::assertEquals(true, $elem->hasAttribute('level'));
        static::assertEquals(1, $elem->getAttribute('level'));
    }

    /**
     * Проверка функционала поиска обязательного элемента. Проверка ситуации когда элемент не найден
     *
     * @expectedException \OldTown\Workflow\Exception\NotExistsRequiredElementException
     * @expectedExceptionMessage Отсутствует элемен invalid-element
     *
     */
    public function testGetRequiredChildElementException()
    {
        /** @var \DOMElement  $elem */
        $elem = static::$xpath->query('//testGetRequiredChildElement')->item(0);
        XmlUtil::getRequiredChildElement($elem, 'invalid-element');
    }

    /**
     * Проверка получения нескольких элементов
     */
    public function testGetChildElements()
    {
        /** @var \DOMElement  $elem */
        $elem = static::$xpath->query('//testGetChildElements')->item(0);
        $elements = XmlUtil::getChildElements($elem, 'element1');

        static::assertEquals(3, count($elements));

        $attr = [
            '1',
            '1_1',
            '1_2',
        ];

        foreach ($elements as $elem) {
            static::assertEquals(true, $elem->hasAttribute('level'));
            $value = $elem->getAttribute('level');

            static::assertEquals(true, in_array($value, $attr, true));
        }
    }


    /**
     * Проверка получения текста
     */
    public function testGetText()
    {
        /** @var \DOMElement  $elem */
        $elem = static::$xpath->query('//testGetText')->item(0);
        $text = XmlUtil::getText($elem);

        $expected = 'test_text_1test_text_2';

        static::assertEquals($expected, $text);
    }


    /**
     * Проверка получения текста
     */
    public function testGetChildText()
    {
        /** @var \DOMElement  $elem */
        $elem = static::$xpath->query('//testGetChildText')->item(0);
        $text = XmlUtil::getChildText($elem, 'element1');

        $expected = 'test_text_1test_text_2';

        static::assertEquals($expected, $text);
    }


    /**
     * Проверка получения значения атрибута
     */
    public function testGetRequiredAttributeValue()
    {
        /** @var \DOMElement  $elem */
        $elem = static::$xpath->query('//testGetRequiredAttributeValue/element1')->item(0);
        $text = XmlUtil::getRequiredAttributeValue($elem, 'level');

        static::assertEquals('1', $text);
    }


    /**
     * Проверка получения значения атрибута
     *
     * @expectedException \OldTown\Workflow\Exception\NotExistsRequiredAttributeException
     */
    public function testGetRequiredAttributeValueAttributeNotExists()
    {
        /** @var \DOMElement  $elem */
        $elem = static::$xpath->query('//testGetRequiredAttributeValue/element1')->item(0);
        XmlUtil::getRequiredAttributeValue($elem, 'not exists attribute');
    }


    /**
     * Тест заглушка. Реализовать в дальнейшем
     *
     */
    public function testEncode()
    {
        $expected = 'test';
        static::assertEquals($expected, XmlUtil::encode($expected));
    }
}
