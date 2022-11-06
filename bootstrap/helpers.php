<?php

/**
 * returning true if our IP
 */
if (!function_exists('IsDev')) {
    function IsDev()
    {
        return in_array(
            $_SERVER["REMOTE_ADDR"] ?? '0.0.0.0',
            [
                '127.0.0.1',
                '178.134.27.166',
                '185.70.52.79',
                '46.49.127.237',
                '85.114.250.94',
                '46.49.118.104',
                '192.168.69.81',
                '5.178.249.186',
                '192.168.69.68',
                '92.54.194.184',
                '192.168.69.65',
                '95.104.101.10',
                '178.134.244.30',
                '192.168.69.121',
                '93.177.131.144',
                '212.58.121.222',
                '188.169.16.211',
                '178.134.229.149',
                '188.121.205.206',
                '78.40.104.37',
                '31.146.115.100',
                '212.58.102.25',
                '172.19.0.1',
                '185.115.7.17' //Temporary, SHOULD BE REMOVED!.

            ]
        );
    }
}
if (!function_exists('dev')) {
    function dev()
    {
        return IsDev();
    }
}

if (!function_exists('extract_up')) {
    function extract_up($array = NULL, $param = NULL)
    {
        if (is_null($array) || is_null($param)) {
            return false;
        }

        $depth = 5;
        for ($i = 0; $i < $depth; $i++) {
            if (isset($array[$param])) return $array[$param];
            if (!isset($array[0])) {

                return null;
            }
            $array = $array[0];
        }

        return null;
    }
}

if (!function_exists('hours')) {
    function hours($number = 1)
    {
        return 60 * 60 * $number;
    }
}

if (!function_exists('minutes')) {
    function minutes($number = 1)
    {
        return 60 * $number;
    }
}

if (!function_exists('parseAsInteger')) {
    function parseAsInteger($string = NULL, $int = NULL)
    {
        if (is_null($string) || is_null($int)) {
            return false;
        }

        return str_replace('?i', $int, $string);
    }
}

/**
 * @param $object
 * @return mixed
 */
if (!function_exists('stdToArr')) {
    function stdToArr($object)
    {
        return json_decode(json_encode($object), true);
    }
}


if (!function_exists('ddd')) {
    function ddd($var)
    {
        if (isDev()) {
            dd($var);
        }
    }
}

/**
 * Returns global request object
 */
if(!function_exists('request')) {
    function request() {
        return app('request');
    }
}

if(!function_exists('VIEW_TYPE_ID')) {
    function VIEW_TYPE_ID($type) {
        if(is_null($type)) {
            return false;
        }

        $types =  config('settings.VIEW_TYPE_IDS');
        
        return array_key_exists($type, $types) ? $types[$type] : null;
    }
}

if(!function_exists('DATA_TYPE_ID')) {
    function DATA_TYPE_ID($type) {
        if(is_null($type)) {
            return false;
        }
        
        $types =  config('settings.DATA_TYPE_IDs');
        
        return array_key_exists($type, $types) ? $types[$type] : null;
    }
}