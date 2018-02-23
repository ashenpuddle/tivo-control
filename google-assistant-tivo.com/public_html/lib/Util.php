<?php
/**
 * Apache License
 * Version 2.0, January 2004
 * http://www.apache.org/licenses/
 */

/**
 * Created by PhpStorm.
 * User: michael
 * Date: 7/21/17
 * Time: 7:50 PM
 */

namespace Voice;

class Util {
    private static $give = array();
    /**
     * @var global $wpdb
     */
    private static $wpdb;

    /**
     * Give a variable to be accessed globally
     *
     * @param $name  The name of the variable (how it will also be "retrieved"
     * @param $value The value to be assigned
     */
    static function give($name, $value) {
        self::$give[$name] = $value;

        if ($value === null) {
            unset(self::$give[$name]);
        }
    }

    /**
     * Receive a value of a variable previously set using give();
     *
     * @param $name    The name of the variable to be retrieved
     * @param $default The default value to return if index is not set
     *
     * @return mixed|null returns the value of the variable called, else null if it is not set
     */
    static function receive($name, $default = null) {
        return (isset(self::$give[$name]) ? self::$give[$name] : $default);
    }

    /**
     * Pretty version of @see var_dump()
     */
    static function v() {
        if (VOICE_DEBUG) {
            if (function_exists('xdebug_get_code_coverage')) {
                foreach (func_get_args() as $arg) {
                    var_dump($arg);
                }
            }
            else {
                foreach (func_get_args() as $arg) {
                    echo '<pre>';
                    var_dump($arg);
                    echo '</pre>';
                }
            }
        }
    }

    /**
     * Output debug (or any) info to the page
     */
    static function d() {
        if (VOICE_DEBUG) {
            foreach (func_get_args() as $arg) {
                echo '<pre>';
                echo $arg;
                echo '</pre>';
            }
        }
    }

    /**
     * Pretty version of @see print_r()
     */
    static function p() {
        if (VOICE_DEBUG) {
            foreach (func_get_args() as $arg) {
                if (is_array($arg)) {
                    echo '<pre>';
                    print_r($arg);
                    echo '</pre>';
                }
                else {
                    self::v($arg);
                }
            }
        }
    }

    public static function camelCase($input, $separator = '_') {
        /**
         * we cannot take advantage of ucwords second parameter (delimiter)
         * because WPEngine doesn't like it :'(
         */
        return str_replace(' ', '', ucwords(str_replace($separator, ' ', $input)));
    }

    public static function camelCase2Snake($input) {
        $pieces = preg_split('/(?=[A-Z])/', $input);
        return trim(strtolower(implode('_', $pieces)), '_');
    }

    /**
     * @return global wpdb
     */
    public static function wpdb() {
        if (empty(self::$wpdb)) {
            global $wpdb;
            self::$wpdb = $wpdb;
        }

        return self::$wpdb;
    }

    public static function mysqlTimestamp() {
        return date('Y-m-d H:i:s');
    }

    public static function object2Array($object) {
        if (!is_object($object) && !is_array($object)) {
            return $object;
        }

        return array_map(array('Util', 'object2Array'), (array)$object);
    }

    public static function email_template($templateName, $userData) {
        $templates = get_field('email_templates', 'option');

        foreach ($templates as $template) {
            if ($template['name'] === $templateName) {
                return str_replace(array_keys($userData), array_values($userData), $template['content']);
            }
        }
    }

    /**
     * Join a string with a natural language conjunction at the end.
     * https://gist.github.com/angry-dan/e01b8712d6538510dd9c
     *
     * nl_implode = Natural Language Implode
     */
    public static function nl_implode($delimiter = ', ', array $list, $conjunction = 'and') {
        $last = array_pop($list);
        if ($list) {
            return implode($delimiter, $list) . ', ' . $conjunction . ' ' . $last;
        }
        return $last;
    }

    public static function findArrayValueByKey($array, $search) {
        if (!is_array($array)) return false;

        while ($array) {
            if (isset($array[$search])) return $array[$search];
            $segment = array_shift($array);
            if (is_array($segment)) {
                if ($return = self::findArrayValueByKey($segment, $search)) return $return;
            }
        }

        return false;
    }
}