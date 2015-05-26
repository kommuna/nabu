<?php

namespace Nabu\Exceptions;

class Forbidden403 extends APIException {
    protected $httpCode = 403;
    protected $message = 'Forbidden';
}
