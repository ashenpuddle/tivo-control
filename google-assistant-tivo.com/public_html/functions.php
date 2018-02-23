<?php
define('VOICE_DEBUG', true);

require_once('lib/Util.php');
require_once('lib/Voice_Controller_Request.php');
require_once('lib/Voice_Controller_Response.php');
require_once('lib/Voice_Model_Request.php');
require_once('lib/Voice_Model_Response.php');

/**
 * Thanks buddel
 * @link http://php.net/manual/en/function.array-search.php#91365
 *
 * @param $needle
 * @param $haystack
 * @return bool|int|string
 */
function recursive_array_search($needle, $haystack) {
    foreach ($haystack as $key => $value) {
        $current_key = $key;
        if ($needle === $value OR (is_array($value) && recursive_array_search($needle, $value) !== false)) {
            return $current_key;
        }
    }
    return false;
}

function requestLog($message = '') {
    shell_exec('echo "' . $message . '" >> ../logs/request.log');
}


function logObject($message = '', $object) {
    requestLog($message . ': [');
    ob_start();
    var_dump($object);
    $debug_dump = ob_get_clean();
    requestLog($debug_dump);
    requestLog(']');
}

function getJSONStatus() {
    switch (json_last_error()) {
    case JSON_ERROR_NONE:
        requestLog(' - No errors');
        break;
    case JSON_ERROR_DEPTH:
        requestLog(' - Maximum stack depth exceeded');
        break;
    case JSON_ERROR_STATE_MISMATCH:
        requestLog(' - Underflow or the modes mismatch');
        break;
    case JSON_ERROR_CTRL_CHAR:
        requestLog(' - Unexpected control character found');
        break;
    case JSON_ERROR_SYNTAX:
        requestLog(' - Syntax error, malformed JSON');
        break;
    case JSON_ERROR_UTF8:
        requestLog(' - Malformed UTF-8 characters, possibly incorrectly encoded');
        break;
    default:
        requestLog(' - Unknown error');
        break;
    };
}
