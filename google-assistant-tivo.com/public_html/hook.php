<?php
/**
 * Apache License
 * Version 2.0, January 2004
 * http://www.apache.org/licenses/
 *
 * Author: Michael Dahlke
 * URL: http://www.michaeldahlke.com
 *      https://bitbucket.org/mdahlke/
 */

ob_start();

require 'functions.php';
require 'lookups.php';

/**
 * IP Address of your TiVo
 *
 * @var string $ip
 */
$ip = '192.168.86.107';

/**
 * Port your TiVo is listening on
 * (this most likely should not change)
 *
 * @var string $port
 */
$port = '31339';

/**
 * The name of your request log file
 *
 * @var string $requestLog
 */
$requestLog = '/var/www/google-assistant-tivo.com/logs/request.log';
$commandAlreadyRan = false;
$executedCommand = false;

$signalType = [
    'channel' => 'SETCH',
    'ir' => 'IRCODE',
    'teleport' => 'TELEPORT',
    'keyboard' => 'KEYBOARD',
];

$teleport = [
             'TIVO', 
             'LIVETV', 
             'GUIDE', 
             'NOWPLAYING', 
             'NETFLIX', 
             'HBOGO', 
             'YOUTUBE',
             'AMAZON'
            ];

$ircode = ['UP', 'DOWN', 'LEFT', 'RIGHT', 'SELECT', 'TIVO', 'LIVETV', 'GUIDE', 'INFO', 'EXIT', 'THUMBSUP', 'THUMBSDOWN',
    'CHANNELUP', 'CHANNELDOWN', 'MUTE', 'VOLUMEDOWN', 'VOLUMEUP', 'TVINPUT',
    'VIDEO_MODE_FIXED_480i', 'VIDEO_MODE_FIXED_480p', 'VIDEO_MODE_FIXED_720p', 'VIDEO_MODE_FIXED_1080i', 'VIDEO_MODE_HYBRID', 'VIDEO_MODE_HYBRID_720p', 'VIDEO_MODE_HYBRID_1080i', 'VIDEO_MODE_NATIVE',
    'CC_ON', 'CC_OFF', 'OPTIONS', 'ASPECT_CORRECTION_FULL', 'PLAY', 'FORWARD', 'REVERSE', 'PAUSE', 'SLOW', 'REPLAY', 'ADVANCE', 'RECORD'
    , 'NUM0', 'NUM1', 'NUM2', 'NUM3', 'NUM4', 'NUM5', 'NUM6', 'NUM7', 'NUM8', 'NUM9', 'ENTER', 'CLEAR',
    'ACTION_A', 'ACTION_B', 'ACTION_C', 'ACTION_D'];

$sequenceCommands = ['UP', 'DOWN', 'LEFT', 'RIGHT', 'SELECT'];

$keyboard = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'space', 'minus', 'equals', 'lbracket', 'rbracket',
    'backslash', 'semicolon', 'quote', 'comma', 'period', 'slash', 'backquote', 'home', 'end', 'kbdup', 'kbddown', 'kbdleft', 'kbdright', 'pageup', 'pagedown', 'caps', 'lshift', 'rshift',
    'insert', 'backspace', 'delete', 'kbdenter', 'stop', 'video_on_demand'];

$requestTime = date('Y/m/d H:i:sa');

requestLog('--------REQUEST: ' . $requestTime . ' --------');

$json = file_get_contents('php://input'); //http://php.net/manual/en/wrappers.php.php

requestLog('\$json: [' . $json . ']');
$request = json_decode($json, true);
getJSONStatus();

/**
$VoiceRequest = new Voice\Voice_Controller_Request($request);
$VoiceResponse = new Voice\Voice_Controller_Response();

$VoiceResponse->model()->setResponseObject([
    'type' => 0,
    'speech' => 'I don\'t know what you want me to do',
]);

$voiceIntent = $VoiceRequest->model()->getIntent();

$explodeResult = explode('.', $voiceIntent);
logObject('\$explodeResult', $explodeResult);
*/

requestLog('\$request["command"]: [' . $request["command"] . ']');
list($iftttCommand, $iftttChannel) = explode(' ', $request["command"]);

requestLog('iftttCommand: [' . $iftttCommand. ']');
requestLog('iftttChannel: [' . $iftttChannel. ']');

$iftttCommand = strtolower($iftttCommand);
$tivoCommand = $signalType[$iftttCommand];
requestLog('\$tivoCommand: [' . $tivoCommand . ']');

$channelName = strtolower($iftttChannel);
requestLog('\$channelName: [' . $channelName . ']');

$channelNumber = $channelLookup[$channelName];
requestLog('\$channelNumber: [' . $channelNumber. ']');

if (!empty($channelNumber) && !$commandAlreadyRan) {
    // send the command to the shell for execution
    $theCommand = 'echo ' . $tivoCommand. ' ' . strtoupper($channelNumber) . ' | telnet ' . $ip . ' ' . $port;
    $executedCommand = shell_exec($theCommand);
}

/**
if ($codeType === 'setch') {
    $signal = $signalType['channel'];
    $command = null;
    $channelResponse = null;

    if ($action === 'channel') {
        $command = filter_var($VoiceRequest->model()->getArguments('channelNumber'), FILTER_VALIDATE_INT);
        $channelResponse = $command;
    }
    else {
        $channelResponse = $VoiceRequest->model()->getArguments('channelName');
        $channelName = strtolower($VoiceRequest->model()->getArguments('channelName'));
        $alias = recursive_array_search($channelName, $channelAliases);

        // does the request ask for channel by name?
        if ((!empty($alias) && $channelName = (isset($channelLookup[$alias]) ? $alias : null)) || isset($channelLookup[$channelName])) {
            $command = $channelLookup[$channelName];
        }

    }

    if ($command) {
        $VoiceResponse->model()->setResponseObject([
            'type' => 0,
            'speech' => 'Okay, ' . $channelResponse
        ]);
    }

}
else if ($codeType === 'ircode') {
    $signal = $signalType['ir'];
    $command = null;
    $originalCommand = $VoiceRequest->model()->getArguments('command');
    $channelResponse = null;

    // does the request ask for channel by name?
    if ((!empty($alias) && $channelName = (isset($channelLookup[$alias]) ? $alias : null)) || isset($channelLookup[$channelName])) {
        $command = $channelLookup[$channelName];
    }
    $command = str_replace(' ', '', $originalCommand);

    if (!in_array(strtoupper($command), $ircode)) {
        $isAlias = recursive_array_search($originalCommand, $commandAliases);

        if ($isAlias) {
            $command = $isAlias;
        }
    }

    if (in_array(strtoupper($command), $ircode)) {
        $VoiceResponse->model()->setResponseObject([
            'type' => 0,
            'speech' => 'Okay, ' . $originalCommand
        ]);
    }
    else if (in_array(strtoupper($command), $teleport)) {
        $signal = $signalType['teleport'];
        $VoiceResponse->model()->setResponseObject([
            'type' => 0,
            'speech' => 'Okay, ' . $originalCommand
        ]);
    }
}

else if ($codeType === 'keyboard') {
    $commands = explode(' ', $VoiceRequest->model()->getArguments('keyboard'));

    foreach ($commands as $command) {

        if (in_array($command, $keyboard)) {
            $commandAlreadyRan = true;
            $executedCommand .= shell_exec('echo ' . $signalType['keyboard'] . ' ' . strtoupper($command) . ' | telnet ' . $ip . ' ' . $port);
            usleep(500000);
        }
        elseif (strlen($command) > 1) {
            $subCommands = str_split($command);

            foreach ($subCommands as $subCommand) {
                if (in_array($subCommand, $keyboard)) {
                    $commandAlreadyRan = true;
                    $executedCommand .= shell_exec('echo ' . $signalType['keyboard'] . ' ' . strtoupper($subCommand) . ' | telnet ' . $ip . ' ' . $port);
                    usleep(500000);
                }
            }
        }
    }

    $VoiceResponse->model()->setResponseObject([
        'type' => 0,
        'speech' => 'Okay'
    ]);

}
else if ($codeType === 'sequence') {
    $commands = explode(' ', $VoiceRequest->model()->getArguments('commands'));

    foreach ($commands as $command) {
        $command = strtoupper($command);
        if (in_array($command, $sequenceCommands)) {
            $commandAlreadyRan = true;
            $executedCommand .= shell_exec('echo ' . $signalType['ir'] . ' ' . strtoupper($command) . ' | telnet ' . $ip . ' ' . $port);
            sleep(1);
        }
    }

    $VoiceResponse->model()->setResponseObject([
        'type' => 0,
        'speech' => 'Okay'
    ]);

}

if (!empty($command) && !$commandAlreadyRan) {
    // send the command to the shell for execution
    $theCommand = 'echo ' . $signal . ' ' . strtoupper($command) . ' | telnet ' . $ip . ' ' . $port;
    $executedCommand = shell_exec($theCommand);
}

requestLog('JSON Request: ' . $json);
requestLog('JSON Command: ' . $signal . ' ' . strtoupper($command));
requestLog('--------/REQUEST: ' . $requestTime . ' --------');
*/
ob_get_clean();
header('Content-type: application/json');
echo json_encode($VoiceResponse->model()->getResponseObject(), JSON_HEX_QUOT | JSON_HEX_TAG);
exit;
