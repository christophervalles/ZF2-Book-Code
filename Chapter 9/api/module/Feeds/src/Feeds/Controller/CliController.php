<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Feeds\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Feed\Reader\Reader;

/**
 * This class is the responsible to process the feeds and retrieve new articles
 *
 * @package Wall/Controller
 */
class CliController extends AbstractActionController
{
    /**
     * Hold the table instance
     *
     * @var UserFeedsTable
     */
    protected $userFeedsTable;
    protected $userFeedArticlesTable;
    
    public function processFeedsAction()
    {
        $request = $this->getRequest();
        $verbose = $request->getParam('verbose') || $request->getParam('v');
        
        $userFeedsTable = $this->getTable('UserFeedsTable');
        $userFeedArticlesTable = $this->getTable('UserFeedArticlesTable');
        $feeds = $userFeedsTable->select();
        
        foreach ($feeds as $feed) {
            if ($verbose) {
                printf("Processing feed: %s\n", $feed['url']);
            }
            
            $lastUpdate = strtotime($feed['updated_at']);
            $rss = Reader::import($feed['url']);
            
            // Loop over each channel item/entry and store relevant data for each
            foreach ($rss as $item) {
                $timestamp = $item->getDateCreated()->getTimestamp();
                if ($timestamp > $lastUpdate) {
                    if ($verbose) {
                        printf("Processing item: %s\n", $item->getTitle());
                    }
                    $author = $item->getAuthor();
                    if (is_array($author)) {
                        $author = $author['name'];
                    }
                    
                    $userFeedArticlesTable->create($feed['id'], $item->getTitle(), $item->getContent(), $item->getLink(), $author);
                }
            }
            
            if ($verbose) {
                printf("Updating timestamp\n");
            }
            
            $userFeedsTable->updateTimestamp($feed['id']);
            
            if ($verbose) {
                printf("Finished feed processing\n\n");
            }
        }
    }
    
    /**
     * Get an instance of the tables
     *
     * @param string $table 
     * @return Zend\Db\TableGateway
     * @author Christopher
     */
    protected function getTable($table)
    {
        $sm = $this->getServiceLocator();
        
        switch ($table) {
            case 'UserFeedsTable':
                if (!$this->userFeedsTable) {
                    $this->userFeedsTable = $sm->get('Feeds\Model\UserFeedsTable');
                }
                
                return $this->userFeedsTable;
            case 'UserFeedArticlesTable':
                if (!$this->userFeedArticlesTable) {
                    $this->userFeedArticlesTable = $sm->get('Feeds\Model\UserFeedArticlesTable');
                }
                
                return $this->userFeedArticlesTable;
        }
    }
}