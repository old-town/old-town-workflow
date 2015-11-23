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
use OldTown\Workflow\TransientVars\TransientVarsInterface;
use OldTown\Workflow\WorkflowContextInterface;
use OldTown\Workflow\WorkflowInterface;

/**
 * Class Interpreter
 * @package OldTown\Workflow\Util\PhpShell
 */
class  PhpShellFunctionProvider implements FunctionProviderInterface
{
    /**
     *
     * @param TransientVarsInterface $transientVars
     * @param array $args
     * @param PropertySetInterface $ps
     * @return bool
     *
     * @throws \OldTown\Workflow\Exception\WorkflowException
     * @throws \OldTown\Workflow\Exception\RuntimeException
     * @throws \OldTown\Workflow\Exception\InvalidArgumentException
     */
    public function execute(TransientVarsInterface $transientVars, array $args = [], PropertySetInterface $ps)
    {
        $script = array_key_exists(WorkflowInterface::BSH_SCRIPT, $args) ? $args[WorkflowInterface::BSH_SCRIPT] : '';

        /**@var WorkflowContextInterface $context */
        $context = $transientVars->offsetExists('context')  ? $transientVars['context'] : null;

        /**@var WorkflowEntryInterface $entry */
        $entry = $transientVars->offsetExists('entry') ? $transientVars['entry'] : null;


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
