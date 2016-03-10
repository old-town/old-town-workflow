<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Config;

use OldTown\Workflow\Loader\WorkflowDescriptor;
use OldTown\Workflow\Loader\WorkflowFactoryInterface;
use OldTown\Workflow\Spi\WorkflowStoreInterface;
use OldTown\Workflow\Util\VariableResolverInterface;
use Psr\Http\Message\UriInterface;

/**
 * Interface ConfigurationInterface
 *
 * @package OldTown\Workflow\Config
 */
interface ConfigurationInterface
{
    /**
     * Возвращает true, если фабрика инициализировала объект конфигурации
     *
     * @return boolean
     */
    public function isInitialized();

    /**
     * Проверяется можно ли модифицировать workflow
     *
     * @param string $name имя workflow
     * @return true если workflow можно модифицировать
     */
    public function isModifiable($name);

    /**
     * Возвращает имя класса описвающего хранилидище, в котором сохраняется workflow
     *
     * @return string
     */
    public function getPersistence();

    /**
     * Получить аргументы хранилища
     *
     * @return array
     */
    public function getPersistenceArgs();

    /**
     * Возвращает resolver для работы с переменными
     *
     * @return VariableResolverInterface
     */
    public function getVariableResolver();

    /**
     * Возвращает имя дескриптора workflow
     *
     * @param string $name имя workflow
     *
     * @return WorkflowDescriptor
     */
    public function getWorkflow($name);

    /**
     * Получает список имен всех доступных workflow
     *
     * @return string[]
     */
    public function getWorkflowNames();

    /**
     * Получает хранилище Workflow
     *
     * @return WorkflowStoreInterface
     */
    public function getWorkflowStore();

    /**
     * Загружает указанный файл конфигурации
     *
     * @param UriInterface|null $url
     * @return void
     */
    public function load(UriInterface $url = null);

    /**
     * Удаляет workflow
     *
     * @param string $workflow имя удаляемого workflow
     * @return boolean в случае успешного удаления возвращает true, в противном случае false
     */
    public function removeWorkflow($workflow);

    /**
     * Сохраняет Workflow
     * @param string $name имя сохраняемого workflow
     * @param WorkflowDescriptor $descriptor дескриптор workflow
     * @param boolean $replace - флаг определяющий, можно ли замениить workflow
     * @return boolean
     */
    public function saveWorkflow($name, WorkflowDescriptor $descriptor, $replace = false);

    /**
     * @return WorkflowFactoryInterface
     */
    public function getFactory();
}
