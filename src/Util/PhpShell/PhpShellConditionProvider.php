<?php
/**
 * @link    https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Util\PhpShell;

use OldTown\PropertySet\PropertySetInterface;
use OldTown\Workflow\ConditionInterface;
use OldTown\Workflow\Exception\WorkflowException;
use OldTown\Workflow\Spi\WorkflowEntryInterface;
use OldTown\Workflow\TransientVars\TransientVarsInterface;
use OldTown\Workflow\Util\TextUtils;
use OldTown\Workflow\WorkflowContextInterface;


/**
 * Class PhpShellConditionProvider
 *
 * @package OldTown\Workflow\Util\PhpShell
 */
class  PhpShellConditionProvider implements ConditionInterface, PhpShellProviderInterface
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
    public function passesCondition(TransientVarsInterface $transientVars, array $args = [], PropertySetInterface $ps)
    {
        $script = array_key_exists(static::PHP_SCRIPT, $args) ? $args[static::PHP_SCRIPT] : '';

        /**@var WorkflowContextInterface $context */
        $context = $transientVars->offsetExists('context')  ? $transientVars['context'] : null;

        /**@var WorkflowEntryInterface $entry */
        $entry = $transientVars->offsetExists('entry')  ? $transientVars['entry'] : null;


        $i = new Interpreter($script);

        try {
            $i->setContextParam('entry', $entry);
            $i->setContextParam('context', $context);
            $i->setContextParam('transientVars', $transientVars);
            $i->setContextParam('propertySet', $ps);
            $i->setContextParam('args', $args);


            $o = $i->evalScript();

            if (null === $o) {
                return false;
            } else {
                $oStr = $o;
                if (!settype($o, 'string')) {
                    $errMsg = 'Результат скрипта должен быть строкой либо пребразовываться в строковое значение';
                    throw new WorkflowException($errMsg);
                }
                $result = TextUtils::parseBoolean($oStr);
                return $result;
            }
        } catch (\Exception $e) {
            $errMsg = 'Ошибка выполнения скрипта-условия';
            throw new WorkflowException($errMsg, $e->getCode(), $e);
        }
    }
}
