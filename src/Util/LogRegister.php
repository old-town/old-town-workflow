<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Util;

use OldTown\Log\LogFactory;
use OldTown\PropertySet\PropertySetInterface;
use OldTown\Workflow\Exception\InternalWorkflowException;
use OldTown\Workflow\Exception\WorkflowException;
use OldTown\Workflow\RegisterInterface;
use OldTown\Workflow\Spi\WorkflowEntryInterface;
use OldTown\Workflow\WorkflowContextInterface;

/**
 * Class DefaultVariableResolver
 *
 * @package OldTown\Workflow\Util
 */
class  LogRegister implements RegisterInterface
{
    /**
     * Возвращает объект связанный с переменными запущенного workflow
     *
     * @param WorkflowContextInterface $context контекст workflow
     * @param WorkflowEntryInterface $entry Объект для которого отрабатывает workflow. Может быть пустым
     * @param array $args Аргументы workflow
     * @param PropertySetInterface $ps
     *
     * @throws WorkflowException
     * @return object
     *
     * @throws \OldTown\Workflow\Exception\InternalWorkflowException
     */
    public function registerVariable(WorkflowContextInterface $context, WorkflowEntryInterface $entry, array $args = [], PropertySetInterface $ps)
    {
        $workflowName = 'unknown';
        $workflowId = -1;

        if (null !== $entry) {
            $workflowName = $entry->getWorkflowName();
            $workflowId = $entry->getId();
        }

        $groupByInstance = false;
        if (array_key_exists('addInstanceId', $args)) {
            $groupByInstance = TextUtils::parseBoolean($args['addInstanceId']);
        }


        $categoryName = 'OSWorkflow';

        if (array_key_exists('Category', $args)) {
            $categoryName = (string)$args['Category'];
        }
        $category = "{$categoryName}.{$workflowName}";

        if ($groupByInstance) {
            $category .= ".{$workflowId}";
        }

        try {
            $log = LogFactory::getLog($category);
        } catch (\Exception $e) {
            $errMsg = 'Ошибка при инициализации подсистемы логирования';
            throw new InternalWorkflowException($errMsg);
        }



        return $log;
    }
}
