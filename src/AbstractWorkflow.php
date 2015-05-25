<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow;
use OldTown\Workflow\Config\ConfigurationInterface;

/**
 * Class AbstractWorkflow
 *
 * @package OldTown\Workflow
 */
abstract class  AbstractWorkflow implements WorkflowInterface
{
    /**
     * @var WorkflowContextInterface
     */
    protected $context;

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     *
     * @var array
     */
    private $stateCache = [];

    /**
     * @var TypeResolver
     */
    private $typeResolver;
}
