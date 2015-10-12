<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Loader\XMLWorkflowFactory;

use OldTown\Workflow\Loader\WorkflowDescriptor;
use Psr\Http\Message\UriInterface;
use OldTown\Workflow\Exception\RemoteException;

/**
 * Class WorkflowDescriptor
 *
 * @package OldTown\Workflow\Loader\WorkflowConfig
 */
class WorkflowConfig
{
    /**
     *
     * @var string
     */
    const URL_TYPE = 'URL';

    /**
     *
     * @var string
     */
    const FILE_TYPE = 'file';

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
     * @throws RemoteException
     */
    public function __construct($baseDir, $type, $location)
    {
        $this->init($baseDir, $type, $location);
    }

    /**
     * @param $baseDir
     * @param $type
     * @param $location
     * @throws RemoteException
     */
    protected function init($baseDir, $type, $location)
    {
        switch ($type) {
            case (static::URL_TYPE): {
                $uri = $this->uriFactory($location);
                $this->url = $uri;

                $uriString = (string)$uri;
                $meta = get_headers($uriString, 1);

                $lastModified = time();
                if (array_key_exists('Last-Modified', $meta)) {
                    $lastModified = $meta['Last-Modified'];
                    $date = \DateTime::createFromFormat("D, d M Y H:i:s \G\M\T", $lastModified);
                    $lastModified = $date->getTimestamp();
                }

                $this->lastModified = $lastModified;

                break;
            }
            case (static::FILE_TYPE): {
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
