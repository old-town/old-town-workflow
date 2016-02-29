<?php
/**
 * @link    https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Util;

use OldTown\PropertySet\PropertySetInterface;
use OldTown\Workflow\Exception\InternalWorkflowException;
use OldTown\Workflow\TransientVars\TransientVarsInterface;

/**
 * Class DefaultVariableResolver
 *
 * @package OldTown\Workflow\Util
 */
class  DefaultVariableResolver implements VariableResolverInterface
{
    /**
     * Регулярка для поиска перменных в аргументе
     *
     * @var string
     */
    protected $variablePatterns = '/.*?(\\${[a-zA-Z_\\x7f-\\xff][a-zA-Z0-9_.\\x7f-\\xff]*})/i';

    /**
     *
     * @param string                 $s
     * @param TransientVarsInterface $transientVars
     * @param PropertySetInterface   $ps
     *
     * @return mixed
     *
     * @throws \OldTown\Workflow\Exception\InternalWorkflowException
     * @throws \OldTown\PropertySet\Exception\PropertyException
     */
    public function translateVariables($s, TransientVarsInterface $transientVars, PropertySetInterface $ps)
    {
        if (0 === strpos($s, '${') && '}' === substr($s, -1) && 1 === substr_count($s, '$')) {
            $var = substr($s, 2, -1);

            return $this->getVariableFromMaps($var, $transientVars, $ps);
        }

        $resultVariable = $s;

        $matches = [];
        preg_match_all($this->variablePatterns, $s, $matches);


        if (array_key_exists(1, $matches) && is_array($matches[1])) {
            $prepareVariables = $matches[1];
            $variables = array_unique($prepareVariables);

            $pattern = [];
            $replacement = [];
            foreach ($variables as $variable) {
                $variableName = substr($variable, 2, -1);
                $pattern[] = $variable;

                $variableValue = $this->getVariableFromMaps($variableName, $transientVars, $ps);
                $replacement[] = (string)$variableValue;
            }

            $resultVariable = str_replace($pattern, $replacement, $s);
        }

        return $resultVariable;
    }

    /**
     * @param mixed                  $var
     * @param TransientVarsInterface $transientVars
     * @param PropertySetInterface   $ps
     *
     * @return mixed
     * @throws \OldTown\PropertySet\Exception\PropertyException
     * @throws \OldTown\Workflow\Exception\InternalWorkflowException
     */
    public function getVariableFromMaps($var, TransientVarsInterface $transientVars, PropertySetInterface $ps)
    {
        $firstDot = strpos($var, '.');
        $actualVar = $var;

        if (false !== $firstDot) {
            $actualVar = substr($var, 0, $firstDot);
        }


        $o = null;
        if ($transientVars->offsetExists($actualVar)) {
            $o = $transientVars->offsetGet($actualVar);
        }

        if (null === $o) {
            $o = $ps->getAsActualType($actualVar);
        }

        if (false !== $firstDot) {
            $property = substr($var, $firstDot + 1);
            $o = $this->getPropertyObject($o, $property);
        }

        return $o;
    }

    /**
     * @param mixed       $object
     * @param string|null $property
     *
     * @return mixed
     *
     * @throws \OldTown\Workflow\Exception\InternalWorkflowException
     */
    protected function getPropertyObject($object = null, $property = null)
    {
        if (null === $object || null === $property || !is_object($object)) {
            return null;
        }

        $property = (string)$property;

        $st = explode('.', $property);

        $result = $object;

        foreach ($st as $currentPropertyName) {
            try {
                $result = $this->invokeProperty($result, $currentPropertyName);
            } catch (\Exception $e) {
                $errMsg = sprintf(
                    'Ошика при получение свойства %s',
                    $currentPropertyName
                );
                throw new InternalWorkflowException($errMsg, $e->getCode(), $e);
            }
        }

        return $result;
    }

    /**
     * @param mixed       $obj
     * @param string|null $property
     *
     * @return mixed
     */
    protected function invokeProperty($obj, $property = null)
    {
        if (null === $property) {
            return null;
        }

        if (!settype($property, 'string')) {
            return null;
        }

        $property = trim($property);
        if (0 === mb_strlen($property)) {
            return null;
        }

        if (!is_object($obj)) {
            return null;
        }

        $methods = [
            $this->createMethodName('get', $property),
            $this->createMethodName('is', $property),
            $property
        ];

        $r = new \ReflectionObject($obj);
        foreach ($methods as $method) {
            if ($r->hasMethod($method)) {
                $result = $r->getMethod($method)->invoke($obj);

                return $result;
            }
        }

        if ($r->hasProperty($property)) {
            $rProperty = $r->getProperty($property);
            if ($rProperty->isPublic()) {
                $result = $rProperty->getValue($obj);

                return $result;
            }
        }

        return null;
    }

    /**
     * Подготавливает название метода
     *
     * @param $prefix
     * @param $propertyName
     *
     * @return string
     */
    protected function createMethodName($prefix, $propertyName)
    {
        return $prefix . ucfirst($propertyName);
    }
}
