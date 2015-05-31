<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Loader;


use OldTown\Workflow\Exception\FactoryException;
use OldTown\Workflow\Exception\InvalidWorkflowDescriptorException;
use OldTown\Workflow\Util\Properties\Properties;
use Psr\Http\Message\UriInterface;
use Serializable;


/**
 * Class UrlWorkflowFactory
 *
 * @package OldTown\Workflow\Loader
 */
class  XmlWorkflowFactory extends AbstractWorkflowFactory implements Serializable
{
    /**
     * @var array
     */
    protected $workflows;

    /**
     * @var bool
     */
    protected $reload = false;

    /**
     * Пути по умолчнаию до файла с workflows
     *
     * @var array
     */
    protected static $defaultPathsToWorkflows = [];

    /**
     * @param Properties $p
     */
    public function __construct(Properties $p = null)
    {
        parent::__construct($p);
        $this->initDefaultPathsToWorkflows();

    }

    /**
     * Иницализация путей по которым происходит поиск
     *
     * @return void
     */
    protected function initDefaultPathsToWorkflows()
    {
        static::$defaultPathsToWorkflows[] = __DIR__ . '/../../config';
    }


    /**
     * @param string $workflowName
     * @param object $layout
     * @return $this
     */
    public function setLayout($workflowName, $layout)
    {

    }

    /**
     * @param string $workflowName
     * @return object|null
     */
    public function getLayout($workflowName)
    {
        return null;
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
     *
     * @return void
     * @throws FactoryException
     */
    public function initDone()
    {
        $this->reload = (boolean)$this->getProperties('reload', false);

        $name = $this->getProperties()->getProperty('resource', 'workflows.xml');

        $contentWorkflowFile = $this->getContentWorkflowFile($name);

        var_dump($contentWorkflowFile);


        die();

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
     * @param string $name
     *
     * @return string
     */
    protected function getContentWorkflowFile($name)
    {

        $paths = static::getDefaultPathsToWorkflows();

        $content = null;
        foreach ($paths as $path) {
            $path = realpath($path);
            if ($path) {
                $filePath = $path . DIRECTORY_SEPARATOR .$name;
                if (file_exists($filePath)) {
                    $content = file_get_contents($filePath);
                    break;
                }
            }
        }

        if (null === $content) {
            $errMsg = 'Не удалось прочитать конфигурационный файл';
            throw new FactoryException($errMsg);
        }

        return $content;
    }

    /**
     * @return array
     */
    public static function getDefaultPathsToWorkflows()
    {
        return self::$defaultPathsToWorkflows;
    }

    /**
     * @param array $defaultPathsToWorkflows
     */
    public static function setDefaultPathsToWorkflows(array $defaultPathsToWorkflows = [])
    {
        self::$defaultPathsToWorkflows = $defaultPathsToWorkflows;
    }

    /**
     * @param string $path
     */
    public static function addDefaultPathToConfig($path)
    {
        $path = (string)$path;

        array_unshift(self::$defaultPathsToWorkflows, $path);
    }

    ##########################################################################################################



    /**
     * @param string $name
     * @return boolean
     */
    public function isModifiable($name)
    {
        return false;
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


}

