<?php

namespace Users\Validator;

use Zend\Validator\AbstractValidator;

class Url extends AbstractValidator
{
    const INVALID   = 'urlInvalid';

    /**
     * @var array
     */
    protected $messageTemplates = array(
        self::INVALID   => "Invalid url given"
    );

    /**
     * Returns true if the given string is a valid url
     *
     * @param string $value 
     * @return boolean
     */
    public function isValid($value)
    {
        if (!is_string($value)) {
            $this->error(self::INVALID);
            return false;
        }
        
        $this->setValue($value);
        if(!filter_var($value, FILTER_VALIDATE_URL)) {
            $this->error(self::INVALID);
            return false;
        }
        
        return true;
    }
}