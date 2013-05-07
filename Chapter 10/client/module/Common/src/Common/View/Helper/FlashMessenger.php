<?php

namespace Common\View\Helper;

use Zend\Form\View\Helper\AbstractHelper;
use Zend\View\Helper\ViewModel;

class FlashMessenger extends AbstractHelper
{
    private static $sucessTemplate = '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>%s</div>';
    
    public function __invoke()
    {
        $viewVars = reset($this->getView()->viewModel()->getCurrent()->getChildren());
        $messages = $viewVars->flashMessages ?: array();
        
        $html = '';
        foreach ($messages as $msg) {
            $html .= sprintf(self::$sucessTemplate, $msg);
        }
        
        return $html;
    }
}