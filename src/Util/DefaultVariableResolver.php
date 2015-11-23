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
     *
     * @param string               $s
     * @param TransientVarsInterface                $transientVars
     * @param PropertySetInterface $ps
     *
     * @return mixed
     *
     * @throws \OldTown\Workflow\Exception\InternalWorkflowException
     * @throws \OldTown\PropertySet\Exception\PropertyException
     */
    public function translateVariables($s, TransientVarsInterface $transientVars, PropertySetInterface $ps)
    {
        $temp = trim($s);

        if (0 === strpos($temp, '${') && '}' === substr($s, -1) && 1 === substr_count($temp, '$')) {
            $var = substr($temp, 2, -1);
            $result = $this->getVariableFromMaps($var, $transientVars, $ps);

            return $result;
        }

        $count = 0;
        while (true) {
            $x = strrpos($s, '${');

            if (false === $x) {
                break;
            }


            $y = strpos($s, '}', $x);


            if (false !== $y) {
                $var = substr($s, $x + 2, $y - $x - 2);


                $t = null;
                $o = $this->getVariableFromMaps($var, $transientVars, $ps);

                if (null !== $o && settype($o, 'string')) {
                    $t = $o;
                }

                if (null !== $t) {
                    $s = substr($s, 0, $x) . $t . substr($s, $y + 1);
                } else {
                    $s = substr($s, 0, $x) . substr($s, $y + 1);
                }
            } else {
                break;
            }

            $count++;
            if ($count > 2000) {
                $errMsg = 'Ошибка при разрешение переменных';
                throw new InternalWorkflowException($errMsg);
            }
        }

        return $s;
    }

    /**
     * @param mixed                $var
     * @param TransientVarsInterface                $transientVars
     * @param PropertySetInterface $ps
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
        if ($transientVars instanceof \ArrayObject && array_key_exists($actualVar, $transientVars)) {
            $o = $transientVars[$actualVar];
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
     * @param mixed $obj
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
        $method = $prefix . ucfirst($propertyName);

        return $method;
    }
}
