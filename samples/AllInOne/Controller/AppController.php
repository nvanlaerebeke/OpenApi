<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('OpenApiAppController', 'OpenApi.Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends OpenApiAppController {
    /**
     * When set this will override the default links that get returned
     * @var string[]
     */
    public $Links = null;
    
    public $components = array(
        'OpenApi.ApiAuth',
        'RequestHandler' => array(
            "loginRedirect" => false,
            "logoutRedirect" => false,
            "autoRedirect" => false,
            "unauthorizedRedirect" => false
        ), 
        'Paginator'
    );
    
    public function beforeFilter() {
        parent::beforeFilter();
        
        $paginate = array();
        $limit = Configure::read('OpenApi.Paging.Limit');
        foreach($this->uses as $model) {
            $paginate[$model] = array(
                'findType' => 'apilist',
                'limit' => (isset($this->params->query['limit'])) ? $this->params->query['limit'] : ((!empty($limit)) ? $limit : 10),
                'contain' => array($model),
                'fields' => (isset($this->params->query['fields'])) ? $this->params->query['fields'] : null,
                'page' => (isset($this->params->query['page'])) ? $this->params->query['page'] : null,
            );
        }
        $this->Paginator->settings = $paginate;
    }
   
    /**
     * Called after the controller action is run, but before the view is rendered. You can use this method
     * to perform logic or set view variables that are required on every request.
     *
     * @return void
     * @link http://book.cakephp.org/2.0/en/controllers.html#request-life-cycle-callbacks
     */
    public function beforeRender() {
        parent::beforeRender();

        $response = array_merge(array('links' => $this->GetLinks()), ((isset($this->viewVars['response'])) ? $this->viewVars['response'] : array()));
        
        //xml always needs a 'container' to make it valid in case multiple root elements are returned 
        if($this->RequestHandler->request->params['ext'] == 'xml') {
            $response = array('response' => $response); 
        }

        // -- Trigger auto output with set type
        $this->set('response', $response);
        if(!file_exists(ROOT.DS.APP_DIR.DS.'View'.DS.$this->viewPath.DS.$this->RequestHandler->request->params['ext'].DS.$this->view.'.ctp')) {
            $this->set('_serialize', 'response');
        }
    }
    
    protected function GetLinks() {
        //Links got set by the User, use those instead of the auto generated ones
        if(!empty($this->Links)) {
            return $this->Links;
        }

        if(isset($this->request->params['paging'][$this->name])) {
            $this->params->query['page'] = $this->request->params['paging'][$this->name]['page'];
            $this->params->query['limit'] = $this->request->params['paging'][$this->name]['limit'];
            
            $links['self'] = 'http://'.$_SERVER['HTTP_HOST'].$this->request->here. '?' . http_build_query($this->params->query);
            
            if(!empty($this->params['paging'][$this->name]['prevPage'])) {
                $this->params->query['page']--;
                $links['previous'] = 'http://'.$_SERVER['HTTP_HOST'].$this->request->here. '?' . http_build_query($this->params->query);
                $this->params->query['page']++;
            }
            
            if(!empty($this->params['paging'][$this->name]['nextPage'])) {
                $this->params->query['page']++;
                $links['next'] = 'http://'.$_SERVER['HTTP_HOST'].$this->request->here. '?' . http_build_query($this->params->query);
                $this->params->query['page']--;
            }
        } else {
            $links['self'] = 'http://'.$_SERVER['HTTP_HOST'].$this->request->here. ((!empty($this->params->query)) ? '?' . http_build_query($this->params->query) : '');            
        }
        return $links;
    }
}