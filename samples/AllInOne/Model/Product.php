<?php
/**
 * Import the base model class for OpenApi
 */ 
App::uses('OpenApiModel', 'OpenApi.Model');
class Product extends OpenApiModel {
    public $primaryKey = 'ProductID';
}
