<?php

/**
 * Channel lookup based on string value
 * You will need to update these for your viewing area
 *
 * @since v1.0.0
 *
 * @var array $channelLookup
 */
$channelLookup = [
    'pbs' => 918,
    'abc' => 908,
    'cbs' => 912,
    'nbc' => 906,
    'fox' => 910,
    'cw' => 914,
    'hallmark' => 987,
    'discovery' => 936,
    'tlc' => 933,
    'tnt' => 925,
    'tbs' => 939,
    'usa' => 938,
    'hgtv' => 983,
    'food network' => 984,
    'espn' => 923,
    'fox sports' => 969,
    'amc' => 955
];

/**
 * Channel aliases so you can say just "Discovery" instead of "Discovery Channel"
 *
 * @since v1.0.0
 *
 * @var array $channelAliases
 */
$channelAliases = [
    'pbs' => ['public broadcasting station'],
    'hsn' => ['home shopping network'],
    'hgtv' => ['home and garden television'],
    'disovery channel' => ['discovery'],
    'food network' => ['food'],
    'espn' => ['sports center', 'sports'],
];

$commandAliases = [
    'ACTION_A' => ['a', 'action a', 'action air'],
    'ACTION_B' => ['a', 'action b'],
    'ACTION_C' => ['c', 'action c'],
    'ACTION_D' => ['d', 'action d'],
    'CC_ON' => ['closed captions on', 'closed captioning', 'closed captioning on'],
    'CC_OFF' => ['closed captions off', 'closed captioning off'],
    'NUM0' => ['number 0', 'num 0', 'number zero', 'num zero'],
    'NUM1' => ['number 1', 'num 1', 'number one', 'num one'],
    'NUM2' => ['number 2', 'num 2', 'number two', 'num two'],
    'NUM3' => ['number 3', 'num 3', 'number three', 'num three'],
    'NUM4' => ['number 4', 'num 4', 'number four', 'num four'],
    'NUM5' => ['number 5', 'num 5', 'number five', 'num five'],
    'NUM6' => ['number 6', 'num 6', 'number six', 'num six'],
    'NUM7' => ['number 7', 'num 7', 'number seven', 'num seven'],
    'NUM8' => ['number 8', 'num 8', 'number eight', 'num eight'],
    'NUM9' => ['number 9', 'num 9', 'number nine', 'num nine'],
    'THUMBSUP' => ['i like this', 'like', 'like this'],
    'THUMBSDOWN' => ['i don\'t like this', 'don\'t like', 'don\'t like this', 'i do not like this', 'i do not like', 'do not like'],
    'MUTE' => ['shutup', 'quite', 'shh'],
];
