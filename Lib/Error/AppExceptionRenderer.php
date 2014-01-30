<?php 
/**
 * Api exception renderer class
 *
 * Copyright (c) 
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) 
 * @link          http://blog.crazytje.com
 * @package       OpenApi.Lib.Error
 * @since         OpenApi v0.0.1
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * Include the cake ExceptionRenderer class
 */
App::uses('ExceptionRenderer', 'Error');

/**
 * Exception Renderer for OpenApi
 *
 * Subclass of ExceptionRenderer giving control over how Exceptions are rendered
 *
 * Sets the ExitCode and ExitMessage on the controller on exceptions
 * 
 * @package       OpenApi.Lib.Error
 */
class AppExceptionRenderer extends ExceptionRenderer {
    /**
     * Creates the controller to perform rendering on the error response.
     * If the error is a CakeException it will be converted to either a 400 or a 500
     * code error depending on the code used to construct the error.
     *
     * @param Exception $exception Exception
     * @return mixed Return void or value returned by controller's `appError()` function
     */
    protected function _getController($exception) {
        if (!$request = Router::getRequest(true)) {
            $request = new CakeRequest();
        }
        $response = new CakeResponse();
        if (method_exists($exception, 'responseHeader')) {
            $response->header($exception->responseHeader());
        }
        
        $controller = Configure::read('OpenApi.ErrorController');
        if(empty($controller)) {
            App::uses('ApiErrorController', 'OpenApi.Controller');
            $controller =  'ApiErrorController';
        } else {
            App::uses($controller, 'Controller');
        }

        try {
            $controller = new $controller($request, $response);
            if(empty($controller)) { throw new Exception('Error controller cannot be found'); }

            $controller->startupProcess();
         } catch (Exception $e) {
            if (!empty($controller) && $controller->Components->enabled('RequestHandler')) {
                $controller->RequestHandler->startup($controller);
            }
        }
        $controller->ExitCode = $exception->getCode();
        $controller->ExitMessage = $exception->getMessage();
        return $controller;
    }
}