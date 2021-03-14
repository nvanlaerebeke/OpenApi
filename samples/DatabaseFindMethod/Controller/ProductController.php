<?php
/**
 * Import base controller class
 */
App::uses('AppController', 'Controller');

/**
 * Serie Controller, provides Api logic for Series
 *
 * @package       OpenApi.Controller
 * @link          http://book.cakephp.org/2.0/en/controllers.html
 */
class ProductController extends AppController {
    /**
     * Auth configuration
     */
    public $AuthConfig = array(
        'index' => array(
            'NoAuth' => array()
        ),
        'view' => array(
            'NoAuth' => array()
        )        
    );

    public function index() {
        $this->set('response', array('Products' => $this->Product->find('apilist')));
        $this->set('_serialize', 'response');
    }
    

    public function view($pID = null) {
        $this->set('response', array('Products' => $this->Product->findByProductid($pID)));
        $this->set('_serialize', 'response');
    }    
}