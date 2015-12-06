<?php
/**
 * @link    https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Loader;

use OldTown\Workflow\Exception\FactoryException;
use OldTown\Workflow\Exception\InvalidParsingWorkflowException;
use OldTown\Workflow\Loader\CallbackWorkflowFactory\WorkflowConfig;


/**
 * Class ArrayWorkflowFactory
 *
 * @package OldTown\Workflow\Loader
 */
class  CallbackWorkflowFactory extends AbstractWorkflowFactory
{
    /**
     *
     * @var string
     */
    const WORKFLOWS_PROPERTY = 'workflows';

    /**
     * @var WorkflowConfig[]
     */
    protected $workflows = [];


    /**
     * @param string $workflowName
     * @param string $layout
     *
     * @return $this
     */
    public function setLayout($workflowName, $layout)
    {
    }

    /**
     * @param string $workflowName
     *
     * @return mixed|null
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
     * @param string $name
     *
     * @return boolean
     */
    public function isModifiable($name)
    {
        return false;
    }

    /**
     *
     * @return String[]
     * @throws FactoryException
     */
    public function getWorkflowNames()
    {
        $workflowNames = array_keys($this->workflows);

        return $workflowNames;
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
     *
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
     * @return void
     * @throws FactoryException
     */
    public function createWorkflow($name)
    {
    }


    /**
     * @param string $name
     * @param bool   $validate
     *
     * @return WorkflowDescriptor
     * @throws FactoryException
     */
    public function getWorkflow($name, $validate = true)
    {
        $name = (string)$name;
        if (!array_key_exists($name, $this->workflows)) {
            $errMsg = sprintf('Нет workflow с именем %s', $name);
            throw new FactoryException($errMsg);
        }
        $c = $this->workflows[$name];

        if (null !== $c->descriptor) {
            return  $c->descriptor;
        }

        $descriptor = call_user_func($c->callback);

        if (!$descriptor instanceof WorkflowDescriptor) {
            $errMsg = sprintf('Ошибка при создание WorkflowDescriptor, для workflow с именем: %s', $name);
            throw new FactoryException($errMsg);
        }

        $c->descriptor = $descriptor;
        $c->descriptor->setName($name);

        return $c->descriptor;
    }


    /**
     * Сохраняет workflow
     *
     * @param string             $name       имя workflow
     * @param WorkflowDescriptor $descriptor descriptor workflow
     * @param boolean            $replace    если true - то в случае существования одноименного workflow, оно будет
     *                                       заменено
     *
     * @return boolean true - если workflow было сохранено
     * @throws FactoryException
     */
    public function saveWorkflow($name, WorkflowDescriptor $descriptor, $replace = false)
    {
        throw new FactoryException('Сохранение workflow не поддерживается');
    }


    /**
     *
     * @return void
     * @throws FactoryException
     * @throws InvalidParsingWorkflowException
     * @throws \OldTown\Workflow\Exception\RemoteException
     */
    public function initDone()
    {
        $workflows = $this->getProperties()->getProperty(static::WORKFLOWS_PROPERTY, null);

        if (null === $workflows) {
            return;
        }

        foreach ($workflows as $name => $workflowItem) {
            $callback = array_key_exists('callback', $workflowItem) ?  $workflowItem['callback'] : null;
            if (!is_callable($callback)) {
                $errMsg = sprintf('Некорректный форма callback\'a для создания workflow с именем %s',  $name);
                throw new FactoryException($errMsg);
            }
            $this->workflows[$name] = new WorkflowConfig($callback);
        }
    }
}
