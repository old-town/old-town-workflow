<?php
/**
 * @link    https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Util\PhpShell;

use OldTown\PropertySet\PropertySetInterface;
use OldTown\Workflow\Exception\WorkflowException;
use OldTown\Workflow\FunctionProviderInterface;
use OldTown\Workflow\Spi\WorkflowEntryInterface;
use OldTown\Workflow\WorkflowContextInterface;
use OldTown\Workflow\WorkflowInterface;

/**
 * Class Interpreter
 * @package OldTown\Workflow\Util\PhpShell
 */
class  FunctionProvider implements FunctionProviderInterface
{

    /**
     *
     * @param array $transientVars
     * @param array $args
     * @param PropertySetInterface $ps
     * @return bool
     *
     * @throws \OldTown\Workflow\Exception\WorkflowException
     */
    public function execute(array $transientVars = [], array $args = [], PropertySetInterface $ps)
    {
        $script = array_key_exists(WorkflowInterface::BSH_SCRIPT, $args) ? $args[WorkflowInterface::BSH_SCRIPT] : '';

        /**@var WorkflowContextInterface $context */
        $context = array_key_exists('context', $transientVars) ? $transientVars['context'] : null;

        /**@var WorkflowEntryInterface $entry */
        $entry = array_key_exists('entry', $transientVars) ? $transientVars['entry'] : null;


        $i = new Interpreter($script);

        try {
            $i->setContextParam('entry', $entry);
            $i->setContextParam('context', $context);
            $i->setContextParam('transientVars', $transientVars);
            $i->setContextParam('propertySet', $ps);
            $i->setContextParam('args', $args);


            $i->evalScript();

        } catch (\Exception $e) {
            $errMsg = 'Ошибка выполнения функции';
            throw new WorkflowException($errMsg, $e->getCode(), $e);
        }
    }
}
