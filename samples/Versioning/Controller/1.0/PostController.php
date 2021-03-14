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
        'delete' => array(
            'NoAuth' => array()
        )
    );

    public function index() {
        $this->set('response', array(
            'Post' => array(
                'author' => 'SomeGuy_1.0',
                'messsage' => 'MyMessage',
                'date' => date('Y-m-d h:i')
            )
        ));
        $this->set('_serialize', 'response');
    }

    public function delete($pID) {
        $this->set('response', array('result' => 'success'));
        $this->set('_serialize', 'response');
    }
}
