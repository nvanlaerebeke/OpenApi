<?php
/**
 * Api invalid param exception class file
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
namespace OpenApi\Error\Exception;

use OpenApi\Error\Exception\OpenApiException;

/**
 * Exception class used for invalid params, based on ApiException
 *
 * @package       OpenApi.Lib.Error
 */
class ValidationException extends OpenApiException {

    /**
     * Constructor for the ApiValidationException
     *
     * The code and message is automatically filled in based on the error type and parameter
     * Code and message are filled in based on the configured class for Api.ErrorClass
     *
     * @var string $pParam
     * @var string $pType
     * @var Exception $pPrevious
     */
    public function __construct($pParam, $pType, $pMessage = null, Exception $pPrevious = null) {
        $message = $pMessage;
        $errorclass = $this->GetErrorClass();
        $code = $errorclass::GetCodeForParam($pParam);
        if($code = 400) {
            $code = $errorclass::GetCodeForValidationError($pType);

            if(empty($message)) {
                $message = $pParam.': '.$errorclass::GetMessageForValidationError($pType);
            }
        }
        parent::__construct($code, $message, $pPrevious);
    }
}