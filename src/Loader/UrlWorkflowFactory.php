<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Loader;

use OldTown\Workflow\Exception\FactoryException;
use OldTown\Workflow\Exception\InvalidWorkflowDescriptorException;
use Psr\Http\Message\UriInterface;
use Serializable;

/**
 * Class UrlWorkflowFactory
 *
 * @package OldTown\Workflow\Loader
 */
class  UrlWorkflowFactory extends AbstractWorkflowFactory implements Serializable
{
    /**
     * @var string
     */
    protected static $uriClassName = '\Zend\Diactoros\Uri';

    /**
     * @var array
     */
    private $cache = [];

    /**
     * @param string $workflowName
     * @param string $layout
     * @return void
     */
    public function setLayout($workflowName, $layout)
    {
    }

    /**
     * @param string $workflowName
     * @return Object
     */
    public function getLayout($workflowName)
    {
        return null;
    }

    /**
     * @param string $name
     * @return boolean
     */
    public function isModifiable($name)
    {
        return false;
    }

    /**
     *
     * @return string
     */
    public function getName()
    {
        return '';
    }


    /**
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     */
    public function serialize()
    {
    }

    /**
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * @return void
     */
    public function unserialize($serialized)
    {
    }

    /**
     * @param string $name
     * @param bool $validate
     *
     * @return WorkflowDescriptor
     * @throws FactoryException
     */
    public function getWorkflow($name, $validate = true)
    {
        $useCacheTxt = $this->getProperties()->getProperty(self::CACHE, 'false');
        $useCache = false;
        if ('true' === $useCacheTxt) {
            $useCache = true;
        }

        if ($useCache && array_key_exists($name, $this->cache)) {
            $descriptor = $this->cache[$name];

            if (null !== $descriptor && !$descriptor instanceof WorkflowDescriptor) {
                $errMsg = "Ошибка при получение workflow {$name} из кеша";
                throw new FactoryException($errMsg);
            }

            if (null !== $descriptor) {
                return $descriptor;
            }
        }

        try {
            $uri = $this->uriFactory($name);
            $descriptor = WorkflowLoader::load($uri);

            if ($useCache) {
                $this->cache[$name] = $descriptor;
            }
        } catch (\Exception $e) {
            $errMsg = "Unable to find workflow {$name}";
            throw new FactoryException($errMsg, $e->getCode(), $e);
        }

        return $descriptor;
    }

    /**
     *
     * @return String[]
     * @throws FactoryException
     */
    public function getWorkflowNames()
    {
        throw new FactoryException('URLWorkflowFactory не содержит имена workflow');
    }


    /**
     * @param string $name
     *
     * @return void
     * @throws FactoryException
     */
    public function createWorkflow($name)
    {
    }


    /**
     * @param string $name
     *
     * @return boolean
     * @throws FactoryException
     */
    public function removeWorkflow($name)
    {
        throw new FactoryException('Удаление workflow не поддерживается');
    }

    /**
     * @param string $oldName
     * @param string $newName
     * @return void
     */
    public function renameWorkflow($newName, $oldName = null)
    {
    }

    /**
     * @return void
     */
    public function save()
    {
    }

    /**
     * Сохраняет workflow
     *
     * @param string $name имя workflow
     * @param WorkflowDescriptor $descriptor descriptor workflow
     * @param boolean $replace если true - то в случае существования одноименного workflow, оно будет заменено
     * @return boolean true - если workflow было сохранено
     * @throws FactoryException
     * @throws InvalidWorkflowDescriptorException
     */
    public function saveWorkflow($name, WorkflowDescriptor $descriptor, $replace)
    {
        //@fixme Организовать созранение workflow в UrlWorkflowFactory
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
