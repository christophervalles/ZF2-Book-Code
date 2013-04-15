<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Wall\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

/**
 * This class is the responsible to answer the requests to the /feeds endpoint
 *
 * @package Wall/Controller
 */
class FeedsController extends AbstractRestfulController
{
    /**
     * Hold the table instance
     *
     * @var UserFeedsTable
     */
    protected $userFeedsTable;
    
    /**
     * Hold the table instance
     *
     * @var UserFeedEntriesTable
     */
    protected $userFeedEntriesTable;
    
    /**
     * Method not available for this endpoint
     *
     * @return void
     */
    public function get($id)
    {
        $userFeedEntriesTable = $this->getTable('UserFeedEntriesTable');
        $entry = $userFeedEntriesTable->getById($id)->toArray();
        
        if (!empty($entry)) {
            return new JsonModel($entry);
        } else {
            throw new \Exception('Article not found', 404);
        }
    }
    
    /**
     * Method not available for this endpoint
     *
     * @return void
     */
    public function getList()
    {
        $userId = $this->params()->fromQuery('user_id');
        $userFeedsTable = $this->getTable('UserFeedsTable');
        $userFeedEntriesTable = $this->getTable('UserFeedEntriesTable');
        
        $feeds = $userFeedsTable->getByUserId($userId)->toArray();
        
        foreach ($feeds as &$f) {
            $entries = array();
            $entries = $userFeedEntriesTable->getByFeedId($f['id'])->toArray();
            $f['entries'] = $entries;
        }
        
        return new JsonModel($feeds);
    }
    
    /**
     * Method not available for this endpoint
     *
     * @return void
     */
    public function create($data)
    {
        $this->methodNotAllowed();
    }
    
    /**
     * Method not available for this endpoint
     *
     * @return void
     */
    public function update($id, $data)
    {
        $this->methodNotAllowed();
    }
    
    /**
     * Method not available for this endpoint
     *
     * @return void
     */
    public function delete($id)
    {
        $this->methodNotAllowed();
    }
    
    protected function methodNotAllowed()
    {
        $this->response->setStatusCode(\Zend\Http\PhpEnvironment\Response::STATUS_CODE_405);
    }
    
    protected function getTable($table)
    {
        $sm = $this->getServiceLocator();
        
        switch ($table) {
            case 'UserFeedsTable':
                if (!$this->userFeedsTable) {
                    $this->userFeedsTable = $sm->get('Wall\Model\UserFeedsTable');
                }
                
                return $this->userFeedsTable;
                break;
            case 'UserFeedEntriesTable':
                if (!$this->userFeedEntriesTable) {
                    $this->userFeedEntriesTable = $sm->get('Wall\Model\UserFeedEntriesTable');
                }
                
                return $this->userFeedEntriesTable;
                break;
        }
    }
}