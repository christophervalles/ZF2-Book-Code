<?php

namespace Wall\PostProcessor;

/**
 * Class used to convert a response into a JSON object
 */
class JsonPostProcessor extends AbstractPostProcessor
{
    /**
     * This is the method that will transform a response into JSON data
     *
     * @return void
     */
    public function process()
    {
        $result = \Zend\Json\Encoder::encode($this->vars);
        $this->response->setContent($result);
        
        $headers = $this->response->getHeaders();
        $headers->addHeaderLine('Content-Type', 'application/json');
        $this->response->setHeaders($headers);
    }
}