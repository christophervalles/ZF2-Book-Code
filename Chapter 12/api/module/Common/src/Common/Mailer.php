<?php

namespace Common;

use Zend\Mail\Message;
use Zend\Mail\Transport\Sendmail;
use Zend\View\Renderer\PhpRenderer;
use Zend\View\Resolver\TemplateMapResolver;
use Zend\View\Model\ViewModel;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;

class Mailer
{
    /**
     * Send a content notification email
     *
     * @param string $toAddress 
     * @param string $toName 
     * @return void
     */
    public static function sendContentNotificationEmail($recipientAddress, $recipientName, $authorName)
    {
        $subject = 'New comment on your wall!';
        $templateVars = array(
            'recipientName' => $recipientName,
            'authorName' => $authorName
        );
        
        self::send($recipientAddress, $recipientName, $subject, 'NewComment', $templateVars);
    }
    
    /**
     * Send the welcome email
     *
     * @param string $toAddress 
     * @param string $toName 
     * @return void
     */
    public static function sendWelcomeEmail($recipientAddress, $recipientName)
    {
        $subject = 'Welcome to My Social Network';
        $templateVars = array(
            'recipientName' => $recipientName
        );
        
        self::send($recipientAddress, $recipientName, $subject, 'WelcomeTemplate', $templateVars);
    }
    
    /**
     * Prepare the resolver with all the templates we have available
     *
     * @return Zend\View\Resolver\TemplateMapResolver
     */
    protected static function initResolver()
    {
        $resolver = new TemplateMapResolver;
        $resolver->setMap(array(
            'MailLayout' => __DIR__ . '/../../view/layout/email-layout.phtml',
            'WelcomeTemplate' => __DIR__ . '/../../view/emails/welcome.phtml',
            'NewComment' => __DIR__ . '/../../view/emails/new-comment.phtml',
        ));
        
        return $resolver;
    }
    
    /**
     * Convenience method to send the emails
     *
     * @param string $toAddress 
     * @param string $toName 
     * @param string $subject 
     * @param string $body 
     * @return void
     */
    protected static function send($toAddress, $toName, $subject, $templateName, $templateVars = array())
    {
        $view = new PhpRenderer;
        $view->setResolver(self::initResolver());
        
        $viewModel = new ViewModel;
        $viewModel->setTemplate($templateName)->setVariables($templateVars);
        $content = $view->render($viewModel);
        
        $viewLayout = new ViewModel;
        $viewLayout->setTemplate('MailLayout')->setVariables(array(
            'content' => $content,
        ));
        
        $html = new MimePart($view->render($viewLayout));
        $html->type = "text/html";
        
        $body = new MimeMessage();
        $body->setParts(array($html));
        
        $mail = new Message;
        $mail->setBody($body);
        $mail->setFrom('no-reply@example.com', 'My social network');
        $mail->addTo($toAddress, $toName);
        $mail->setSubject($subject);
        
        $transport = new Sendmail;
        $transport->send($mail);
    }
}