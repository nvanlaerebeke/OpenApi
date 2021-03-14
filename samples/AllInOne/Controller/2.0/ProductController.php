<?php
App::uses('AppController', 'Controller');

class ProductController extends AppController {
    public $AuthConfig = array(
        'add' => array(
            'EmailPassword' => array(), 
            'IPAddress' => array()
        ),
        'view' => array(
            'NoAuth' => array()
        ),
        'index' => array(
            'NoAuth' => array()
        ),        
        'edit' => array(
            'NoAuth' => array()
        ),
        'delete' => array(
            'EmailPassword' => array(), 
            'IPAddress' => array()
        )
    );

    /**
     * Called before anything else in the Controller
     * Logs the ProductController version we're currently using
     */
    public function beforeFilter() {
        parent::beforeFilter();
        $this->debug("Using version 2.0");
    }

    /**
     * Add a new record to the Product table
     */
    public function add() {
        if(isset($this->data) && isset($this->data['Product'])) {
            $product = $this->Product->save($this->data);
            $this->set('response', array('Products' => $this->Product->findByProductid($product['Product']['ProductID'])));
            $this->Links['self'] = Router::url(array('controller' => $this->name, 'action' => 'view', $product['Product']['ProductID']), true);
        } else {
            throw new ApiException();
        }
    }

    /**
     * Browse all records from the Product table
     */
    public function index() {
        $this->set('response', array('Products' => $this->Paginate('Product')));
    }

    /**
     * View a single record from the Product table 
     */
    public function view($pID) {
        $product = $this->Product->findByProductid($pID);
        if(!empty($product)) {
            $this->set('response', array('Products' => $product));
        } else {
            throw new ApiException(2);
        }
    }
    
    
    /**
     * Edit a record in the Product table
     * 
     * In the PUT request we're expecting JSON input
     * 
     * @param $pID ProductID from the record to update
     * @see http://book.cakephp.org/2.0/en/controllers/request-response.html#accessing-xml-or-json-data 
     */
    public function edit($pID) {
        $data = $this->request->input('json_decode');
        if(!empty($data)) {
            $data->Product->ProductID = $pID;
            if(!$this->Product->save($data)) {
                throw new ApiException();
            }
            $this->Links['self'] = Router::url(array('controller' => $this->name, 'action' => 'view', $data->Product->ProductID), true);
        } else {
            throw new ApiException(2);
        }
    }
    
    /**
     * Make sure to always return a link
     * Some api's use HTTP 204 (no content) but when building a HATEOAS application on top of an api you want the links
     * @see http://en.wikipedia.org/wiki/HATEOAS
     * 
     * @param $pID the ProductID to delete
     */
    public function delete($pID) {
        if(!$this->Product->Delete($pID)) {
            throw new ApiException(1); //'Failed to delete Product'
        }
        $this->Links['self'] = Router::url(array('controller' => $this->name, 'action' => 'index'), true);
    }
}