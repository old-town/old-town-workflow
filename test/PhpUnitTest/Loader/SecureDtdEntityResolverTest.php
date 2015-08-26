<?php
/**
 * @link    https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\PhpUnitTest\Loader;

use PHPUnit_Framework_TestCase as TestCase;
use OldTown\Workflow\Loader\SecureDtdEntityResolver;
use DOMImplementation;

/**
 * Class SecureDtdEntityResolverTest
 *
 * @package OldTown\Workflow\PhpUnitTest\Loader
 */
class SecureDtdEntityResolverTest extends TestCase
{
    /**
     * @var array
     */
    private static $listSchemas = [
        ['workflow_2_5.dtd'],
        ['workflow_2_6.dtd'],
        ['workflow_2_6_1.dtd'],
        ['workflow_2_7.dtd'],
        ['workflow_2_8.dtd'],
    ];

    /**
     * @return array
     */
    public function schemas()
    {
        return static::$listSchemas;
    }


    /**
     * @dataProvider schemas
     *
     * @param $schema
     */
    public function testResolveEntity($schema)
    {

        $imp = new DOMImplementation();
        $dtd  = $imp->createDocumentType(
            'workflow',
            '-//OpenSymphony Group//DTD OSWorkflow 2.8//EN',
            "http://www.opensymphony.com/osworkflow/{$schema}"
        );
        $dom = $imp->createDocument('', '', $dtd);
        $dom->encoding = 'UTF-8';
        $dom->xmlVersion = '1.0';
        $dom->formatOutput = true;

        $secureDtdEntityResolver = new SecureDtdEntityResolver();
        $actualContent = $secureDtdEntityResolver->resolveEntity($dom->doctype);

        $path = $secureDtdEntityResolver->getPathToSchemas();

        $pathToSchema = $path . DIRECTORY_SEPARATOR . $schema;

        if (!(file_exists($pathToSchema) && is_file($pathToSchema) && is_readable($pathToSchema))) {
            $errMsg = sprintf('Invalid path %s', $pathToSchema);
            throw new \RuntimeException($errMsg);
        }

        $expectedContent = file_get_contents($pathToSchema);


        static::assertEquals($expectedContent, $actualContent);

    }

    /**
     * @expectedException \OldTown\Workflow\Exception\InvalidDtdSchemaException
     * @expectedExceptionMessage Отсутствует DOMDocumentType
     *
     * @return void
     */
    public function testResolveEntityInvalidDomDocumentType()
    {
        $secureDtdEntityResolver = new SecureDtdEntityResolver();
        $secureDtdEntityResolver->resolveEntity();
    }


    /**
     * @expectedException \OldTown\Workflow\Exception\InvalidDtdSchemaException
     * @expectedExceptionMessage Некорректный uri doctype: http://www.example.com/
     *
     * @return void
     */
    public function testResolveEntityInvalidDocumentType()
    {
        $imp = new DOMImplementation();
        $dtd  = $imp->createDocumentType(
            'workflow',
            '-//OpenSymphony Group//DTD OSWorkflow 2.8//EN',
            'http://www.example.com/'
        );
        $dom = $imp->createDocument('', '', $dtd);
        $dom->encoding = 'UTF-8';
        $dom->xmlVersion = '1.0';
        $dom->formatOutput = true;

        $secureDtdEntityResolver = new SecureDtdEntityResolver();
        $secureDtdEntityResolver->resolveEntity($dom->doctype);

    }


    /**
     * @expectedException \OldTown\Workflow\Exception\InvalidDtdSchemaException
     * @expectedExceptionMessage Не найдена схема: example.dtd
     *
     * @return void
     */
    public function testResolveEntitySchemaNotFound()
    {
        $imp = new DOMImplementation();
        $dtd  = $imp->createDocumentType(
            'workflow',
            '-//OpenSymphony Group//DTD OSWorkflow 2.8//EN',
            'http://www.opensymphony.com/osworkflow/example.dtd'
        );
        $dom = $imp->createDocument('', '', $dtd);
        $dom->encoding = 'UTF-8';
        $dom->xmlVersion = '1.0';
        $dom->formatOutput = true;

        $secureDtdEntityResolver = new SecureDtdEntityResolver();
        $secureDtdEntityResolver->resolveEntity($dom->doctype);

    }


    /**
     *
     * @return void
     */
    public function testSetUriClassName()
    {
        $current = SecureDtdEntityResolver::getUriClassName();

        $expected = 'test';
        SecureDtdEntityResolver::setUriClassName($expected);

        $actual = SecureDtdEntityResolver::getUriClassName();

        SecureDtdEntityResolver::setUriClassName($current);

        static::assertEquals($expected, $actual);

    }
}
