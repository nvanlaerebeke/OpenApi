<?php
/**
 * class file for the "ErrorController"
 *
 * Copyright (c) 
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Crazy <info@crazytje.com>
 * @link          http://blog.crazytje.com 
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
**/

namespace OpenApi\Controller;

use Cake\Event\Event;
use Cake\Routing\Router;
use Cake\Core\Configure;

/**
 * Class that handles all Error Output
 * We needed to extend the OpenApi AppController so that our output is generated automatically & in the correct format
 * This is a copy of the Core CakeErrorController, for more information, see the Core class docs 
 */
class ErrorController extends \OpenApi\Controller\AppController {
    /**
     * Constructor
     *
     * @param \Cake\Network\Request|null $request Request instance.
     * @param \Cake\Network\Response|null $response Response instance.
     */
    public function __construct($request = null, $response = null) {
        parent::__construct($request, $response);
        if (count(Router::extensions()) && !isset($this->RequestHandler)) {
            $this->loadComponent('RequestHandler');
        }
        $eventManager = $this->eventManager();
        if (isset($this->ApiAuth)) {
            $eventManager->off($this->ApiAuth);
        }
        if (isset($this->Security)) {
            $eventManager->off($this->Security);
        }
    }

    /**
     * Do not apply anything beforeFilter does
     * @ToDo: call the parent without it running the auth process again 
     */
    public function beforeFilter(Event $event) { }

    /**
     * beforeRender callback.
     *
     * @param \Cake\Event\Event $event Event.
     * @return void
     */
    public function beforeRender(Event $event) {
        parent::beforeRender($event);
  
         $this->set([
            'code' => $this->ExitCode,
            'message' => $this->ExitMessage,
            '_serialize' => array ('code', 'message')
        ]);
    }      
}