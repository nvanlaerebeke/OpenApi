<?php
/**
 * Import the base model class for OpenApi
 */ 
App::uses('OpenApiModel', 'OpenApi.Model');
class User extends OpenApiModel {
    public $useDbConfig = 'userdb';
}
