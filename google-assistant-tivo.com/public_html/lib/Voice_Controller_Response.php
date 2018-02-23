<?php
/**
 * Apache License
 * Version 2.0, January 2004
 * http://www.apache.org/licenses/
 */

/**
 * Created by PhpStorm.
 * User: michael
 * Date: 2/24/17
 * Time: 11:36 AM
 */

namespace Voice;

class Voice_Controller_Response {
    private $model;

    public function __construct() {
        if (empty($this->model)) {
            $this->model = new Voice_Model_Response();
        }
    }

    public function talk() {

    }

    public function prepareToTalk() {

    }

    public function setResponse($response) {

    }

    /**
     * @return Voice_Model_Response
     */
    public function model() {
        if (empty($this->model)) {
            $this->model = new Voice_Model_Response();
        }

        return $this->model;
    }

    public function findResponse($responses = null, $defaultResponse = null) {

        switch ($this->model()->getIntentType()) {
            case 'key_value':
                $lookupField = 'key_values';
                break;
            default:
                $lookupField = 'responses';
        }
        if (!$responses) {
            $defaultResponse = get_field('default_response');
            $responses = get_field($lookupField);
        }
        else {
            if (is_object($responses)) {
                $responses = \Voice\Util::object2Array($responses);
            }
            $defaultResponse = $responses['default_response'];
            $responses = $responses[$lookupField];
        }

        if ($responses && count($responses)) {
            if ($this->model()->getIntentType() === 'key_value') {
                $returnResponses = $this->keyValueLookup($responses, $defaultResponse);
            }
            else {
                $returnResponses = $responses;
            }

            $this->model()->setResponses($returnResponses);
        }
        else {
            $this->model()->setResponses($defaultResponse);
        }

        $this->model()->setRepromptType(get_field('reprompt'));

        if (get_field('reprompt') === 'custom') {
            $this->model()->setCommandPrompt(get_field('reprompt_message'));
        }

        $this->filterResponse();
    }

    public function filterResponse() {
        $returnResponses = $this->model()->getResponses();

        if (is_array($returnResponses)) {
            $response = $returnResponses[array_rand($returnResponses)];
        }
        else {
            $this->model()->setIsDefaultResponse(true);
            $response = array(
                'value_text' => $returnResponses,
                'response_type' => 'text',
            );
        }

        $this->model()->mapper($response);

    }

    public function keyValueLookup($values, $default = 'Sorry, try again') {
        global $AiRequest;

        foreach ($values as $value) {
            if (strtolower($value['key']) === strtolower($AiRequest->model()->getArguments('given-name'))) {
                return $value['responses'];
            }
        }

        return $default;
    }

    public function pickResponse() {
        $responses = $this->model()->getResponses();
    }

}