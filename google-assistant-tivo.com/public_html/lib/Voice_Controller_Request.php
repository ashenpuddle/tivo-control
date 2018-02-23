<?php

/**
 * Created by PhpStorm.
 * User: michael
 * Date: 2/24/17
 * Time: 11:36 AM
 */

namespace Voice;

class Voice_Controller_Request {
    protected $model;

    public function __construct($request, $requestFrom = null) {
        if (empty($this->model)) {
            $this->model = Voice_Model_Request::init($request, $requestFrom);
        }
    }

    /**
     * @return Voice_Model_Request
     */
    public function model() {
        return $this->model;
    }

    public function log() {
        /*
        global $AiResponse;
        global $Client;
        global $wpdb;

        ob_start();
        // DON'T REMOVE THIS
        Util::p($AiResponse); // DON'T REMOVE THIS
        // DON'T REMOVE THIS
        $serviceObject = ob_get_clean();
        $data = array(
            'VoiceRequest' => $this->model()->getOriginalRequest(),
            'ClientID' => ($Client instanceof Ai_Model_Voice_Client ? $Client->getClientID() : 0),
            'VoiceRequestService' => $this->model()->getSource(),
            'SessionID' => $this->model()->getSessionID(),
            'VoiceRequestIntent' => $this->model()->getIntent(),
            'VoiceResponse' => $AiResponse->model()->getMessage(),
            'VoiceResponseRaw' => $AiResponse->model()->getMessageRaw(),
            'VoiceServiceObject' => $serviceObject,
            'VoiceResponseObject' => json_encode($AiResponse->model()->getResponseObject()),
        );


         // We check here if this is one of google's "health checks". If it is we don't log it.
        if (Util::findArrayValueByKey($this->model()->getOriginalRequest(), 'name') != 'is_health_check') {
            $wpdb->insert('wp_voice_requests', $data);
        }

        ob_clean();
        */
    }
}