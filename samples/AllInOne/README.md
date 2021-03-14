OpenApi-Samples: All In One
=============================

Content
===============
This sample contains a combination of all previous samples.


Here is a list of what the sample contains:
- Product controller with all CRUD functions implemented
    
    The REST Routing sample implements all CRUD functions.<br />
    In addition to just creating the functions and returning, there is now a database behind it

- Versioning, a 1.0 and 2.0 version of the Product controller

    The functionality from the versioning sample was added. <br />
    Created 2 versions of the Product controller, 1.0 does not contain error codes, 2.0 does
    
- Model where the custom find method is used on

    The sample code from the 'DatabaseFindMethod' was added in the sample.<br />
    No difference accept that it's used in the paging instead of a direct $this-><Model>->find()

- Paging, field filtering and limiting output

    An API will need paging, an example of this was added
    
- Automatically adding pre, self and next links

    When developing a HATEOAS (Hypermedia as the Engine of Application State) client on top of an API you need to always return links.<br /> 
    This is an example of adding self/next and previous links.

- Custom EmailPassword Authenticator that connects to a DB to authenticate

    Based on the Auth sample, an EmailPassword authenticator was added where you can authenticate using Basic authentication.<br />
    The users are fetched from the users.sqlite database
    
- Authorization on controller actions    
    
    A user also has 2 additional parameters called 'allowadd' and 'allowdelete' to show a basic sample of how Authorization can work

- Auto serialization

    This is for ease of use so that the user can always do $this->set('response', <output>) and doesn't have to repeat the _serialize each time 
    
- Custom views

    Sometimes it's necessary to customize output in a view, in this sample this is done for the view action

- Custom error codes

    When developing an API you might want to return pre-defined error codes to a client, this was done for the delete and view actions


Usage
======


CRUD methods
=============
The following requests can be made:
 - View all products:       [GET] http://&lt;yourhost&gt;/&lt;pathtoyourapp&gt;/Product/
 - View a single product    [GET] http://&lt;yourhost&gt;/&lt;pathtoyourapp&gt;/Product/<productid (example 1)>
 - Delete a single product  [DELETE] http://&lt;yourhost&gt;/&lt;pathtoyourapp&gt;/Product/<productid (example 1)>
 - Edit a single product    [PUT] http://&lt;yourhost&gt;/&lt;pathtoyourapp&gt;/Product/<productid (example 1)>
 - Add a product            [POST] http://&lt;yourhost&gt;/&lt;pathtoyourapp&gt;/Product/


Versioning
===========

To test the versioning in this sample you can call version 1.0 and 2.0.<br />
The difference between the 2 is that one does not have any error codes and the other does

Example calls:
 - [GET] http://&lt;yourhost&gt;/&lt;pathtoyourapp&gt;/2.0/product/100
 - [GET] http://&lt;yourhost&gt;/&lt;pathtoyourapp&gt;/1.0/product/100

Note that when no version is supplied, the priority goes from top to bottom(see core.php config), in this case 2.0 was the highest version.

Find Method
===========

The 'apilist' find method is used in the paging.<br />
The paging is done in the AppController:

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
    
Paging
======

Paging is automatically done in the 'index' requests.

To test it use the following GET url's:
 - [GET] http://&lt;yourhost&gt;/&lt;pathtoyourapp&gt;/product/
 - [GET] http://&lt;yourhost&gt;/&lt;pathtoyourapp&gt;/product/?limit=5
 - [GET] http://&lt;yourhost&gt;/&lt;pathtoyourapp&gt;/product/?limit=5&page=2
 - [GET] http://&lt;yourhost&gt;/&lt;pathtoyourapp&gt;/product/?fields[]=ProductID&fields[]=ProductName


Automatically adding links
===========================

In lots of API's the self/next and previous links are required.<br />
This sample also demonstrates how you can easily add these links on the fly without much work.<br />
See the AppController to see how it works.

Here is the code:

    class AppController extends OpenApiAppController {
        public function beforeRender() {
            parent::beforeRender();
    
            $response = array_merge(array('links' => $this->GetLinks()), ((isset($this->viewVars['response'])) ? $this->viewVars['response'] : array()));
            $this->set('response', $response);
            $this->set('_serialize', 'response');
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
    
Custom Authentication & Authorization
======================================

To demonstrate how authentication methods can be added an 'EmailPassword' authenticate class was added that uses basic authentication to authenticate users.<br />
The users are gotten from a users.sqlite database.

In your requests make sure to pass the basic authentication header.<br />
The headers is "Authorization: Basic base64_encode (<username>:<password>)"

There are 3 users in the user table:
 1. email@example.com with password: mypassword
 2. delete@example.com with password: mypassword
 3. add@example.com with password: mypassword 

<br /><br />
All 3 users will pass the authentication process.

The 1st isn't allowed to delete/add products, so with this you can see how the Authorization step works.<br />
The 2nd isn't allowed to add products, but can delete them<br />
The 3rd is only allowed to add products and not delete them.

These are the headers you must provide to do basic authentication

For email@example.com:mypassword<br />
    Authorization: Basic ZW1haWxAZXhhbXBsZS5jb206bXlwYXNzd29yZA==

For delete@example.com:mypassword<br />
    Authorization: Basic ZGVsZXRlQGV4YW1wbGUuY29tOm15cGFzc3dvcmQ=

For add@example.com:mypassword<br />
    Authorization: Basic YWRkQGV4YW1wbGUuY29tOm15cGFzc3dvcmQ=
    

Example requests:
 - [DELETE] http://&lt;yourhost&gt;/&lt;pathtoyourapp&gt;/product/1
 - [POST] http://&lt;yourhost&gt;/&lt;pathtoyourapp&gt;/product/

Make sure to post the Product correctly, you must at least post a ProductName.

Example:

    Product[ProductName] = MyNewProduct

Auto serialization
==================

There is not much to this, the trick is to always set your response to the same view var so that in the AppController beforeRender we can _serialize the same variable each time

    public function beforeRender() {
        parent::beforeRender();
        $this->set('_serialize', 'response');
    }
    
Custom views
============

Using custom views is just a matter of not doing the serialization and having the view files(.ctp) in the correct location.<br />
To get this all you need to do is add an 'if' around the serialize when the view file is found:

    public function beforeRender() {
        parent::beforeRender();
        if(!file_exists(ROOT.DS.APP_DIR.DS.'View'.DS.$this->viewPath.DS.$this->RequestHandler->request->params['ext'].DS.$this->view.'.ctp')) {
            $this->set('_serialize', 'response');
        }
    }

To see this working, do the following requests:
 - [GET] http://&lt;yourhost&gt;/&lt;pathtoyourapp&gt;/product/1
 
You'll see that the response had an additional 'CustomVIewFile => true' in the response.<br />
The custom files are located in "app/View/Product/(xml/json)/view.ctp".


Custom error codes
==================
In the 2.0 version of the product controller we've added several custom error codes.

You can try it be doing a view request for a none existent product:
 - [GET] http://&lt;yourhost&gt;/&lt;pathtoyourapp&gt;/product/999

You'll see the error code 2 with the message "Product not found".<br />
This was done by configuring a custom ApiError class in core.php:

    Configure::write('OpenApi.ErrorClass', 'OpenApiCustomApiError');

The custom error class is located in 'app/Lib/Error/OpenApiCustomApiError.php':
    
    App::uses('BaseApiError', 'OpenApi.Lib/Error');

    class OpenApiCustomApiError extends BaseApiError {
   
        protected static $__codes = array(
            1 => 'Unable to delete product',
            2 => 'Product not found'
        );
    }