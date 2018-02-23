<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 2/24/17
 * Time: 1:51 PM
 */

namespace Voice;

class Voice_Model_Response {
    private $actionName;
    private $message;
    private $messageRaw;
    private $isDefaultResponse;
    private $responses;
    private $type;
    private $intentType;
    private $apiUrl;
    private $apiResponseTemplate;
    private $apiRequestType;
    private $apiRequestHeaders;
    private $responseObject;
    private $commandPrompt;
    private $repromptType;

    /**
     * @return mixed
     */
    public function getActionName() {
        return $this->actionName;
    }

    /**
     * @param mixed $actionName
     */
    public function setActionName($actionName) {
        $this->actionName = $actionName;
    }

    /**
     * @return mixed
     */
    public function getMessage() {
        if (empty($this->message)) {
            $this->setMessage('We don\'t have this information yet.');
            $this->setCommandPrompt('Try asking another question.');
        }

        return $this->message;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message) {
        global $AiRequest;

        $parameters = $AiRequest->model()->getArguments();
        foreach ($parameters as $key => $val) {
            $message = str_replace('%' . $key . '%', $parameters[$key], $message);
        }

        $this->message = $message;
    }

    public function getMessageRaw() {
        return $this->messageRaw;
    }

    public function setMessageRaw($message) {
        $this->messageRaw = $message;
    }

    /**
     * @return bool
     */
    public function isIsDefaultResponse() {
        if (empty($this->isDefaultResponse)) {
            $this->setIsDefaultResponse(false);
        }

        return $this->isDefaultResponse;
    }

    /**
     * @param bool $isDefaultResponse
     */
    public function setIsDefaultResponse($isDefaultResponse) {
        $this->isDefaultResponse = $isDefaultResponse;
    }

    /**
     * @return mixed
     */
    public function getResponses() {
        return $this->responses;
    }

    /**
     * @param mixed $responses
     */
    public function setResponses($responses) {
        $this->responses = $responses;
    }

    /**
     * @return mixed
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type) {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getIntentType() {
        return $this->intentType;
    }

    /**
     * Type of the intent (basic text | key_value lookup)
     *
     * @param mixed $intentType
     */
    public function setIntentType($intentType) {
        $this->intentType = $intentType;
    }

    /**
     * @return mixed
     */
    public function getResponseObject() {

        if (empty($this->responseObject)) {
            $this->setResponseObject(array('message' => 'responseObject not set'));
        }

        return $this->responseObject;
    }

    /**
     * @param array $responseObject
     */
    public function setResponseObject($responseObject) {
        $this->responseObject = $responseObject;
    }

    /**
     * @return mixed
     */
    public function getApiUrl() {
        return $this->apiUrl;
    }

    /**
     * @param mixed $apiUrl
     */
    public function setApiUrl($apiUrl) {
        $this->apiUrl = $apiUrl;
    }

    /**
     * @return mixed
     */
    public function getApiResponseTemplate() {
        if (empty($this->apiResponseTemplate)) {
            $this->setApiResponseTemplate('generic');
        }

        return $this->apiResponseTemplate;
    }

    /**
     * @return mixed
     */
    public function getCommandPrompt() {
        if ($this->commandPrompt === null || $this->commandPrompt === false && $this->getRepromptType() === 'default') {
            $this->setCommandPrompt(DEFAULT_REPROMPT_MESSAGE);
        }
        return $this->commandPrompt;
    }

    /**
     * @param mixed $commandPrompt
     */
    public function setCommandPrompt($commandPrompt) {
        $this->commandPrompt = $commandPrompt;
    }

    /**
     * @param mixed $apiResponseTemplate
     */
    public function setApiResponseTemplate($apiResponseTemplate) {
        $acceptableTemplates = array('generic', 'list');
        if (!in_array($apiResponseTemplate, $acceptableTemplates)) {
            $foundSuitableReplacement = false;
            foreach ($acceptableTemplates as $at) {
                if (strpos($apiResponseTemplate, $at) !== false) {
                    $foundSuitableReplacement = true;
                    $apiResponseTemplate = $at;
                }
            }
            if (!$foundSuitableReplacement) {
                // no suitable template found
                // set the template to the generic template
                $apiResponseTemplate = 'generic';
            }
        }
        $this->apiResponseTemplate = $apiResponseTemplate;
    }

    /**
     * @return mixed
     */
    public function getApiRequestType() {
        return $this->apiRequestType;
    }

    /**
     * @param mixed $apiRequestType
     */
    public function setApiRequestType($apiRequestType) {
        $this->apiRequestType = $apiRequestType;
    }

    /**
     * @return mixed
     */
    public function getApiRequestHeaders() {
        return $this->apiRequestHeaders;
    }

    /**
     * @param mixed $apiRequestHeaders
     */
    public function setApiRequestHeaders($apiRequestHeaders) {
        if (is_array($apiRequestHeaders)) {
            $headers = array();
            foreach ($apiRequestHeaders as $header) {
                $headers[$header['key']] = $header['value'];
            }
        }
        $this->apiRequestHeaders = $headers;
    }

    /**
     * @return mixed
     */
    public function getRepromptType() {
        return $this->repromptType;
    }

    /**
     * @param mixed $repromptType
     */
    public function setRepromptType($repromptType) {
        $this->repromptType = $repromptType;
    }

    public function __construct($response = null) {
        if ($response) {
            $this->mapper($response);
        }
    }

    public function mapper($response = array()) {
        $map = array(
            'action_name' => 'actionName',
            'responses' => 'responses',
            'response_type' => 'type',
            'value_text' => 'messageRaw',
            'intent_plugin' => 'intentPlugin',
        );
        if ($response) {
            foreach ($response as $key => $val) {
                $key = \Voice\Util::camelCase($map[$key]);
                call_user_func(array($this, 'set' . $key), $val);
            }
            $this->sanitizeResponse();
        }

    }

    public function sanitizeResponse() {
        switch ($this->getType()) {
            case 'audio':
                $audioObject = false;
                if (is_array($this->getAudio())) {
                    $audioObject = $this->getAudio();
                    $audio = $this->getAudio()['url'];
                }
                else if (is_int($this->getAudio())) {
                    $audioObject = get_media_item($this->getAudio());
                    $audio = $audioObject['url'];
                }
                else {
                    $audio = $this->getAudio();
                }

                $this->setAudioObject($audioObject);
                $this->setAudio($this->buildAudioResponse($audio));
                break;
            case 'text_audio':
                $audioObject = false;
                if (is_array($this->getAudio())) {
                    $audioObject = $this->getAudio();
                    $audio = $this->getAudio()['url'];
                }
                else if (is_int($this->getAudio())) {
                    $audioObject = get_media_item($this->getAudio());
                    $audio = $audioObject['url'];
                }
                $this->setAudioObject($audioObject);
                $this->setAudio($this->buildAudioResponse($this->getAudio()['url']));
                $this->setMessage($this->getMessageRaw());
                break;
            default:
                $sanitized = $this->getMessageRaw();
                $this->setMessage($sanitized);
        }

        return $sanitized;
    }

    public function buildAudioResponse($source) {
        global $ngrokUrl;

        return '<audio src="' . str_replace('http://www.wisnet-voice-ai-client.dev', $ngrokUrl, $source) . '"></audio>';
    }

    public function setUnknownIntentResponse() {
        global $Ai;
        $params = array(
            'value_text' => 'Sorry, we don\'t know that intent yet',
            'response_type' => 'text',
        );
        $this->setResponses(new Voice_Model_Response($params));
    }
}