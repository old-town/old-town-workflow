<?php
/**
 * @link    https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\PhpUnitTest\Loader;


use PHPUnit_Framework_TestCase as TestCase;
use OldTown\Workflow\Loader\WorkflowLoader;
use Psr\Http\Message\UriInterface;


/**
 * Class WorkflowLoaderTest
 *
 * @package OldTown\Workflow\PhpUnitTest\Loader
 */
class WorkflowLoaderTest extends TestCase
{
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

    public function testLoadFromUrl()
    {
        $url = 'file:///path/to/file/test.xml';
        $uri = self::uriFactory($url);

        $descriptor = WorkflowLoader::load($uri);
    }
}
