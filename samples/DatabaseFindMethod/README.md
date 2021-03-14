OpenApi-Samples: Custom Database Find Method
=============================================

Sample content
===============
This sample demonstrates a custom find method that OpenApi has.<br />
The find method is very useful for generating xml output, as from the default 'all' find method you can't generate good xml 

The sample uses the northwind sqlite database, it's located in the Config directory.<br />
See https://code.google.com/p/northwindextended/ 

This sample assumes you've read and understand the 'Basic Sample'


Usage
=====

Point your browser to the following url's:
 - [GET] http://&lt;yourhost&gt;/&lt;pathtoyourapp&gt;/product/
 - [GET] http://&lt;yourhost&gt;/&lt;pathtoyourapp&gt;/product/1
 
The get the output will be:

    <?xml version="1.0" encoding="UTF-8"?>
    <Products>
        <Product>
            <ProductID>1</ProductID>
            ...
            <Discontinued>0</Discontinued>
        </Product>
        <Product>
            ...
        </Product>
    </Products>
    
For the delete the output will be:

    <?xml version="1.0" encoding="UTF-8"?>
    <Products>
        <Product>
            ...
        </Product>
    </Products>
    
How it's done
==============

Using the extra find method is as simple as making your Models extend OpenApiModel and using 'apilist' as find method.


Step 1: Create the Model
========================

The model is standard, nothing much to see except we're extending from OpenApiModel instead of AppModel

    App::uses('OpenApiModel', 'OpenApi.Model');
    class Product extends OpenApiModel { }


Step 2: Create the controller
==============================

The controller class has 2 methods in this sample, the index and view.<br />
The index method uses the 'apilist' find method instead of 'all'.<br />
The view method just queries for a single record.


    class ProductController extends AppController {
        /**
         * Auth configuration
         */
        public $AuthConfig = array(
            'index' => array(
                'NoAuth' => array()
            ),
            'view' => array(
                'NoAuth' => array()
            )        
        );
    
        public function index() {
            $this->set('response', array('Products' => $this->Product->find('apilist')));
            $this->set('_serialize', 'response');
        }
        
    
        public function view($pID = null) {
            $this->set('response', array('Products' => $this->Product->findByProductid($pID)));
            $this->set('_serialize', 'response');
        }    
    }