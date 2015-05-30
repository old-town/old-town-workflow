<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Config;
use OldTown\Workflow\Exception\FactoryException;
use OldTown\Workflow\Loader\WorkflowDescriptor;
use OldTown\Workflow\Util\VariableResolverInterface;
use OldTown\Workflow\Spi\WorkflowStoreInterface;
use OldTown\Workflow\Exception\StoreException;


/**
 * Interface ConfigurationInterface
 *
 * @package OldTown\Workflow\Config
 */
class  DefaultConfiguration implements ConfigurationInterface
{
    /**
     * @var DefaultConfiguration
     */
    protected static $instance;

    /**
     * @var array
     */
    private $cache = [];

    /**
     * Флаг определяющий было ли иницилизированно workflow
     *
     * @var bool
     */
    private $initialized = false;

    /**
     * @param string $workflowName
     * @param object $layout
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
        return "";
    }

    /**
     * Возможность статически получать экземпляр конфигурации по умолчанию для workflow
     *
     * @return DefaultConfiguration
     */
    public static function getInstance()
    {
        if (self::$instance instanceof self) {
            return self::$instance;
        }

        self::$instance = new self();

        return self::$instance;
    }

    /**
     * Возвращает true, если фабрика инициализировала объект конфигурации
     *
     * @return boolean
     */
    public function isInitialized()
    {
        return $this->initialized;
    }

    /**
     * Загружает указанный файл конфигурации
     *
     * @param string $url
     * @return void
     * @throws FactoryException
     */
    public function load($url)
    {

    }
########################################################################################################################
#Методы заглушки, при портирование заменять на реализацию ##############################################################
########################################################################################################################




    /**
     * Возвращает имя класса описвающего хранилидище, в котором сохраняется workflow
     *
     * @return string
     */
    public function getPersistence()
    {

    }

    /**
     * Получить аргументы хранилища
     *
     * @return array
     */
    public function getPersistenceArgs()
    {

    }

    /**
     * Возвращает resolver для работы с переменными
     *
     * @return VariableResolverInterface
     */
    public function  getVariableResolver()
    {

    }

    /**
     * Возвращает имя дескриптора workflow
     *
     * @param string $name имя workflow
     * @throws FactoryException
     * @return WorkflowDescriptor
     */
    public function  getWorkflow($name)
    {

    }

    /**
     * Получает список имен всех доступных workflow
     * @throws FactoryException
     * @return String[]
     */
    public function  getWorkflowNames()
    {

    }

    /**
     * Получает хранилище Workflow
     *
     * @return WorkflowStoreInterface
     * @throws StoreException
     */
    public function getWorkflowStore()
    {

    }

    /**
     * Удаляет workflow
     *
     * @param string $workflow имя удаляемого workflow
     * @return boolean в случае успешного удаления возвращает true, в противном случае false
     * @throws FactoryException
     */
    public function removeWorkflow($workflow)
    {

    }

    /**
     * Сохраняет Workflow
     * @param string $name имя сохраняемого workflow
     * @param WorkflowDescriptor $descriptor дескриптор workflow
     * @param boolean $replace - флаг определяющий, можно ли замениить workflow
     * @throws FactoryException
     * @return boolean
     */
    public function  saveWorkflow($name, WorkflowDescriptor $descriptor, $replace = false)
    {

    }
}
