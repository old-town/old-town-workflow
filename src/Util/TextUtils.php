<?php
/**
 * @link https://github.com/old-town/old-town-workflow
 * @author  Malofeykin Andrey  <and-rey2@yandex.ru>
 */
namespace OldTown\Workflow\Util;


/**
 * Class TextUtil
 * @package OldTown\Workflow\Util
 */
class  TextUtils
{
    /**
     * @param $in
     * @return bool|string
     */
    public static function parseBoolean($in)
    {
        $value = $in;
        if (!is_string($value)) {
            if (!settype($value, 'boolean')) {
                return false;
            }
            return $value;
        }

        $value = trim($value);
        if (0 === strlen($value)) {
            return false;
        }

        $firstSymbol = substr($value, 0, 1);
        $firstSymbol = strtolower($firstSymbol);

        switch ($firstSymbol) {
            case '1':
            case 't':
            case 'y': {
                return true;
            }
            default: {
                return false;
            }

        }


    }

}
