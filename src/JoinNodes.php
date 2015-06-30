<?php
/**
 * @link    https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow;

use OldTown\Workflow\Exception\InternalWorkflowException;
use \OldTown\Workflow\JoinNodes\DummyStep;
use OldTown\Workflow\Spi\StepInterface;

/**
 * Class JoinNodes
 *
 * @package OldTown\Workflow
 */
class JoinNodes
{
    /**
     * @var StepInterface[]
     */
    protected $steps;

    /**
     * @var DummyStep
     */
    protected $dummy;

    /**
     * @param array $steps
     *
     * @throws \OldTown\Workflow\Exception\InternalWorkflowException
     */
    public function __construct(array $steps = [])
    {
        foreach ($steps as $step) {
            if (!$step instanceof StepInterface) {
                $errMsg = 'Некорректная коллекция из шагов workflow';
                throw new InternalWorkflowException($errMsg);
            }
        }

        $this->dummy = new DummyStep();
        $this->steps = $steps;
    }

    /**
     * @param $stepId
     *
     * @return DummyStep|StepInterface
     */
    public function getStep($stepId)
    {
        foreach ($this->steps as $step) {
            if ($step->getId() === $stepId) {
                return $step;
            }
        }

        return $this->dummy;
    }
}
