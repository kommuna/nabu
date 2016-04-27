<?php

namespace Nabu\Exceptions;

use Exception;

class NabuException extends Exception {

    protected $errorsArray = [];

    static public function throwException($error) {

        $self = new static();
        $self->addError($error);


        throw $self;
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

        $this->message = json_encode($this->errorsArray);

    }

}