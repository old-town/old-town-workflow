<?php
/**
 * @link    https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Loader;

use OldTown\Workflow\Exception\FactoryException;
use OldTown\Workflow\Exception\InvalidParsingWorkflowException;
use OldTown\Workflow\Loader\XMLWorkflowFactory\WorkflowConfig;


/**
 * Class ArrayWorkflowFactory
 *
 * @package OldTown\Workflow\Loader
 */
class  ArrayWorkflowFactory extends XmlWorkflowFactory
{
    /**
     *
     * @var string
     */
    const WORKFLOWS_PROPERTY = 'workflows';

    /**
     *
     * @return void
     * @throws FactoryException
     * @throws InvalidParsingWorkflowException
     * @throws \OldTown\Workflow\Exception\RemoteException
     */
    public function initDone()
    {
        $this->reload = true === $this->getProperties()->getProperty(static::RELOAD_PROPERTY, false);
        $workflows = $this->getProperties()->getProperty(static::WORKFLOWS_PROPERTY, false);

        $basedir = null;
        foreach ($workflows as $name => $workflowItem) {
            $type = array_key_exists('type', $workflowItem) ?  $workflowItem['type'] : WorkflowConfig::FILE_TYPE;
            $location = array_key_exists('location', $workflowItem) ?  $workflowItem['location'] : '';
            $config = $this->buildWorkflowConfig($basedir, $type, $location);
            $this->workflows[$name] = $config;
        }
    }
}
