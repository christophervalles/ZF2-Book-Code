<?php

namespace Wall\Entity;

use Zend\Stdlib\Hydrator\ClassMethods;
use Wall\Entity\Link;
use Wall\Entity\Image;
use Wall\Entity\Status;

class Wall
{
    protected $feed = array();
    
    public function setFeed($feed)
    {
        $hydrator = new ClassMethods();
        
        foreach ($feed as $entry) {
            if (array_key_exists('status', $entry)) {
                $this->feed[] = $hydrator->hydrate($entry, new Status());
            } else if (array_key_exists('filename', $entry)) {
                $this->feed[] = $hydrator->hydrate($entry, new Image());
            } else if (array_key_exists('url', $entry)) {
                $this->feed[] = $hydrator->hydrate($entry, new Link());
            }
        }
    }
    
    public function getFeed()
    {
        return $this->feed;
    }
}