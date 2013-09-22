<?php

namespace Users\Validator;

use Zend\Validator\AbstractValidator;
use ZendService\Akismet\Akismet;

class Spam extends AbstractValidator
{
    const INVALID = 'invalid';
    const SPAM = 'isSpam';
    
    /**
     * Error messages
     * 
     * @var array
     */
    protected $messageTemplates = array(
        self::INVALID => "Invalid input",
        self::SPAM => "The text seems to be spam"
    );
    
    /**
     * The options available for this validator
     *
     * @var string
     */
    protected $options = array(
        'apiKey' => null,
        'url' => null
    );
    
    /**
     * Sets validator options
     *
     * @param  integer|array|\Traversable $options
     */
    public function __construct($options = array())
    {
        if (!is_array($options)) {
            $options = func_get_args();
            $temp['apiKey'] = array_shift($options);
            if (!empty($options)) {
                $temp['url'] = array_shift($options);
            }
            
            $options = $temp;
        }
        
        parent::__construct($options);
    }
    
    /**
     * Returns the apiKey option
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->options['apiKey'];
    }
    
    /**
     * Sets the apiKey option
     *
     * @param  string $apiKey
     * @throws Exception\InvalidArgumentException
     * @return Spam Provides a fluent interface
     */
    public function setApiKey($apiKey)
    {
        if (empty($apiKey)) {
            throw new \Exception('API key cannot be empty');
        }
        
        $this->options['apiKey'] = $apiKey;
        return $this;
    }
    
    /**
     * Returns the url option
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->options['url'];
    }

    /**
     * Sets the url option
     *
     * @param  string $url
     * @throws Exception\InvalidArgumentException
     * @return Spam Provides a fluent interface
     */
    public function setUrl($url)
    {
        if (empty($url)) {
            throw new \Exception('The url cannot be empty');
        }
        
        $this->options['url'] = $url;
        return $this;
    }
    
    /**
     * Returns true if the given string is not spam
     *
     * @param array $value Should contain the following
     * user_ip
     * user_agent
     * comment_type
     * comment_author
     * comment_author_email
     * comment_content
     *
     * @return boolean
     */
    public function isValid($value)
    {
        if (!is_array($value)) {
            $this->error(self::INVALID);
            return false;
        }
        
        $this->setValue($value);
        $akismet = new Akismet($this->getApiKey(), $this->getUrl());
        if (!$akismet->verifyKey($this->getApiKey())) {
            throw new \Exception('Invalid API key for Akismet');
        }
        
        if ($akismet->isSpam($value)) {
            $this->error(self::SPAM);
            return false;
        } else {
            return true;
        }
    }
}