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
    class PostController extends AppController {
        /**
         * Auth configuration
         */
        public $AuthConfig = array(
            'index' => array(
                'NoAuth' => array()
            ),
            'view' => array(
                'NoAuth' => array()
            ),            
            'delete' => array(
                'NoAuth' => array()
            ),
            'add' => array(
                'NoAuth' => array()
            ),
            'edit' => array(
                'NoAuth' => array()
            )
        );

        public function index() {
            $this->set('response', array(
                'Posts' => array(
                    'Post' => array(
                        array(
                            'id' => 1,
                            'author' => 'SomeGuy',
                            'messsage' => 'MyMessage',
                            'date' => date('Y-m-d h:i')
                        ),
                        array(
                            'id' => 2,
                            'author' => 'SomeGuy2',
                            'messsage' => 'MyMessage2',
                            'date' => date('Y-m-d h:i')
                        )
                    )
                )                
            ));
            $this->set('_serialize', 'response');
        }
    
        public function view($pID) {
            $this->set('response', array(
                'Post' => array(
                    'id' => $pID,
                    'author' => 'SomeGuy',
                    'messsage' => 'MyMessage',
                    'date' => date('Y-m-d h:i')
                )
            ));
            $this->set('_serialize', 'response');
        }

        public function delete($pID) {
            $this->set('response', array('deleted' => 'true'));
            $this->set('_serialize', 'response');
        }
        
        public function add() {
            $this->set('response', array('created' => 'true'));
            $this->set('_serialize', 'response');        
        }
        
        public function edit($pID) {
            $this->set('response', array('edit' => 'true'));
            $this->set('_serialize', 'response');        
        }
    }
