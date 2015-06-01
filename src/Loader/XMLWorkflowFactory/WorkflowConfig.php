<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Loader\XMLWorkflowFactory;

use OldTown\Workflow\Loader\WorkflowDescriptor;
use Psr\Http\Message\UriInterface;
use Serializable;

/**
 * Class WorkflowDescriptor
 *
 * @package OldTown\Workflow\Loader\WorkflowConfig
 */
class WorkflowConfig implements Serializable
{
    /**
     * @var string
     */
    public $location;

    /**
     * Тип - file/url/service
     *
     * @var string
     */
    public $type;

    /**
     * @var UriInterface
     */
    public $url;

    /**
     * @var WorkflowDescriptor
     */
    public $descriptor;

    /**
     * @var
     */
    public $lastModified;

    /**
     * @var string
     */
    protected static $uriClassName = '\Zend\Diactoros\Uri';


    /**
     * @param $baseDir
     * @param $type
     * @param $location
     */
    public function __construct($baseDir, $type, $location)
    {
        $this->init($baseDir, $type, $location);
    }

    /**
     * @param $baseDir
     * @param $type
     * @param $location
     */
    protected function init($baseDir, $type, $location)
    {
        switch ($type) {
            case 'URL': {
                //@fixme task: портировать поддержку подгрузки для url
                $errMsg = 'Работа с Url. Необходимо портировать из оригинального проекта';
                throw new \BadMethodCallException($errMsg);
                break;
            }
            case 'file': {
                $pathToFile = $baseDir . DIRECTORY_SEPARATOR . $location;
                if (file_exists($pathToFile)) {
                    $this->url = realpath($pathToFile);
                    $this->lastModified = filemtime($pathToFile);
                }

                break;
            }
            default: {
                if (file_exists($location)) {
                    $this->url = realpath($location);
                    $this->lastModified = filemtime($location);
                }
                break;
            }
        }

        $this->type = $type;
        $this->location = $location;
    }

    /**
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     */
    public function serialize()
    {

    }

    /**
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized
     * @return void
     */
    public function unserialize($serialized)
    {

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
     * @return string
     */
    public static function getUriClassName()
    {
        return self::$uriClassName;
    }

    /**
     * @param string $uriClassName
     */
    public static function setUriClassName($uriClassName)
    {
        self::$uriClassName = $uriClassName;
    }
}
