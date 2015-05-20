<?php

namespace Nabu\Exceptions;

use Exception;

class APIException extends Exception {

    protected $httpCode = 500;
    protected $errorsArray = [];

    static public function throwException($error) {

        $self = new static();
        $self->addError($error);


        throw $self;
    }

    public function getHTTPCode() {
        return $this->httpCode;
    }

    public function addError($error) {
        if(is_array($error)) {
            $this->errorsArray = $error;    
        } else {
            $this->errorsArray[] = $error;
        }
        $this->refreshMessage();
    }

    public function getErrors() {
        return $this->errorsArray;
    }

    protected function refreshMessage() {

        if($this->errorsArray) {
            $this->message = json_encode($this->errorsArray);
        } else {
            $this->message = "";
        }
    }

}
