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
        'create' => array(
            'NoAuth' => array(
                'FirstName' => 'alphaNumeric',
                'Email' => 'email',
                'Age' => array(
                    'comparison' => array(
                        'rule'    => array('comparison', '>=', 18),
                        'message' => 'Must be at least 18 years old to qualify.'
                    )
                )
            )
        )
    );

    public function add() {
        $this->set('response', array('created' => 'true'));
        $this->set('_serialize', 'response');        
    }
}