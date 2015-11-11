<?php
/**
 * Created by PhpStorm.
 * User: shirshov
 * Date: 11.11.15
 * Time: 16:24
 */
namespace OldTown\Workflow\Util;

use OldTown\PropertySet\PropertySetInterface;
use OldTown\Workflow\FunctionProviderInterface;
use OldTown\Workflow\WorkflowContextInterface;

/**
 * Sets the transient variable "caller" to the current user executing an action.
 */
class Caller implements FunctionProviderInterface
{
    /**
     * @param array $transientVars
     * @param array $args
     * @param PropertySetInterface $ps
     */
    public function execute(array $transientVars = [], array $args = [], PropertySetInterface $ps)
    {
        /** @var WorkflowContextInterface $context */
        $context = $transientVars['context'];
        $transientVars['caller'] = $context->getCaller();
    }
}
