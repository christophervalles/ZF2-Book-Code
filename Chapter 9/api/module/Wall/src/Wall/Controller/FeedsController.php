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
     * Method not available for this endpoint
     *
     * @return void
     */
    public function get($username)
    {
        $this->methodNotAllowed();
    }
    
    /**
     * Method not available for this endpoint
     *
     * @return void
     */
    public function getList()
    {
        $this->methodNotAllowed();
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
}