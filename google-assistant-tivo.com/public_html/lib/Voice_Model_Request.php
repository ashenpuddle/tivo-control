<?php

/**
 * Created by PhpStorm.
 * User: michael
 * Date: 2/24/17
 * Time: 10:19 AM
 */

namespace Voice;

class Voice_Model_Request {
    protected static $instance;
    private $originalRequest;
    private $source;
    private $sessionID;
    private $userID;
    private $arguments = array();
    private $intent;
    private $id;
    private $requestFrom;
    private $timestamp;

    /**
     * @return mixed
     */
    public function getOriginalRequest() {
        return $this->originalRequest;
    }

    /**
     * @param mixed $originalRequest
     */
    public function setOriginalRequest($originalRequest) {
        $this->originalRequest = $originalRequest;
    }

    /**
     * @return mixed
     */
    public function getSource() {
        return $this->source;
    }

    /**
     * @param mixed $source
     */
    public function setSource($source) {
        $this->source = $source;
    }

    /**
     * @return mixed
     */
    public function getSessionID() {
        return $this->sessionID;
    }

    /**
     * @param mixed $sessionID
     */
    public function setSessionID($sessionID) {
        $this->sessionID = $sessionID;
    }

    /**
     * @return mixed
     */
    public function getUserID() {
        return $this->userID;
    }

    /**
     * @param mixed $userID
     */
    public function setUserID($userID) {
        $this->userID = $userID;
    }

    /**
     * @return array|string
     */
    public function getArguments($key = null, $stripSpecialCharacters = false, $strip = '`~!@#$%^&*()_-+={}[]|\:;"\'?/.,<>') {
        if (empty($this->arguments)) {
            $this->arguments = [];
        }

        if ($key) {
            if (isset($this->arguments[$key])) {
                $arg = $this->arguments[$key];
                if ($stripSpecialCharacters) {
                    $chars = str_split($strip);
                    $arg = str_replace($chars, '', $arg);
                }
                return $arg;
            }
            else {
                return false;
            }
        }

        return $this->arguments;
    }

    /**
     * @param array $arguments
     */
    public function setArguments($arguments) {
        $convertedParams = [];
        foreach ($arguments as $key => $val) {
            $convertedParams[$key] = str_replace('\'s', '', $val);
        }

        $this->arguments = $convertedParams;
    }

    public function addArgument($key, $val) {
        $args = $this->getArguments();
        $args[$key] = str_replace('\'s', '', $val);

        $this->setArguments($args);
    }

    /**
     * @return mixed
     */
    public function getIntent() {
        return $this->intent;
    }

    /**
     * @param mixed $intent
     */
    public function setIntent($intent) {
        $this->intent = strtolower($intent);
    }

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getRequestFrom() {
        return $this->requestFrom;
    }

    /**
     * @param mixed $requestFrom
     */
    public function setRequestFrom($requestFrom) {
        $this->requestFrom = $requestFrom;
    }

    /**
     * @return mixed
     */
    public function getTimestamp() {
        return $this->timestamp;
    }

    /**
     * @param mixed $timestamp
     */
    public function setTimestamp($timestamp) {
        $this->timestamp = $timestamp;
    }

    protected function __construct() {
    }

    /**
     * @param array $request
     * @param string $requestFrom
     *
     * @return Ai_Model_Request
     */
    public static function init($request = null, $requestFrom = null) {
        if (self::$instance == null) {
            self::$instance = new Voice_Model_Request();
        }

        if ($request) {
            self::$instance->mapper($request, $requestFrom);
        }

        return self::$instance;
    }

    /**
     * Map the request to the setters
     *
     * @param      $request
     * @param null $requestFrom
     */
    public function mapper($request, $requestFrom = null) {
        if (!isset($request['originalRequest'])) {
            $this->setOriginalRequest(json_encode($request));
            $this->setId('0123456789');
            $this->setSessionID('01234567890123456789');
            $this->setTimestamp(time());
            $this->setIntent($request['result']['action']);
            $this->setArguments($request['result']['parameters']);
            $this->setSource('Test Service');
            $this->setUserID('1');
        }
        else {
            $this->setOriginalRequest(json_encode($request['originalRequest']));
            $this->setId($request['id']);
            $this->setSessionID($request['sessionId']);
            $this->setTimestamp($request['timestamp']);
            $this->setIntent($request['result']['action']);
            $this->setArguments($request['result']['parameters']);
            $this->setSource($request['originalRequest']['source']);
            $this->setUserID($request['originalRequest']['data']['user']['user_id']);
        }
        $this->setRequestFrom($requestFrom);
    }

}