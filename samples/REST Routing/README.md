OpenApi-Samples: REST Routing
=============================

Sample content
===============
This sample demonstrate how the HTTP method (REST) routing works and is configured.
The sample is a continuation from the Basic sample.

Most of this is a sample of what's documented on the CakePHP website:
  http://book.cakephp.org/2.0/en/development/rest.html
    
Example:
========

Create the Controller
=========================

The PostController:

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
            $this->set('response', 
                array(
                    'Posts' => array(
                        'Post' => array(
                            'author' => 'SomeGuy',
                            'messsage' => 'MyMessage',
                            'date' => date('Y-m-d h:i')
                        ), 
                        'Post' => array(
                            'author' => 'SomeGuy',
                            'messsage' => 'MyMessage',
                            'date' => date('Y-m-d h:i')
                        )
                    )
                )
            );
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


Create the Authorizers
======================

As example, the PostController class from above, the 2 Authorize classes are:
 - PostIndexNoAuthAuthorize: located in "Controller/Component/Auth/Authorization/PostIndex/PostIndexNoAuthAuthorize.php"
 - PostViewNoAuthAuthorize: located in "Controller/Component/Auth/Authorization/PostView/PostViewNoAuthAuthorize.php"
 - PostDeleteNoAuthAuthorize: located in "Controller/Component/Auth/Authorization/PostDelete/PostDeleteNoAuthAuthorize.php"
 - PostAddNoAuthAuthorize: located in "Controller/Component/Auth/Authorization/PostAdd/PostAddNoAuthAuthorize.php"
 - PostEditNoAuthAuthorize: located in "Controller/Component/Auth/Authorization/PostEdit/PostEditNoAuthAuthorize.php"

As we'll go over the Auth process in a different sample, for now just return true:

    class PostIndexNoAuthAuthorize extends BaseAuthorize {
        public function authorize($user, CakeRequest $request) {
            return true;
        }
    }

    class PostViewNoAuthAuthorize extends BaseAuthorize {
        public function authorize($user, CakeRequest $request) {
            return true;
        }
    }
    
    class PostDeleteNoAuthAuthorize extends BaseAuthorize {
        public function authorize($user, CakeRequest $request) {
            return true;
        }
    }

    class PostEditNoAuthAuthorize extends BaseAuthorize {
        public function authorize($user, CakeRequest $request) {
            return true;
        }
    }
    
    class PostAddNoAuthAuthorize extends BaseAuthorize {
        public function authorize($user, CakeRequest $request) {
            return true;
        }
    }
    
The result
==========

Point your browser to the following url's WITH the correct HTTP method:
 - [GET] http://&lt;yourhost&gt;/&lt;pathtoyourapp&gt;/post/1
 
        <?xml version="1.0" encoding="UTF-8"?>
        <Post>
            <id>1</id>
            <author>SomeGuy</author>
            <messsage>MyMessage</messsage>
            <date>2014-01-25 07:45</date>
        </Post>
 
 - [GET] http://&lt;yourhost&gt;/&lt;pathtoyourapp&gt;/post/
 
    <?xml version="1.0" encoding="UTF-8"?>
    <Posts>
        <Post>
            <id>1</id>
            <author>SomeGuy</author>
            <messsage>MyMessage</messsage>
            <date>2014-01-25 07:57</date>
        </Post>
        <Post>
            <id>2</id>
            <author>SomeGuy2</author>
            <messsage>MyMessage2</messsage>
            <date>2014-01-25 07:57</date>
        </Post>
    </Posts>
    
 - [POST] http://&lt;yourhost&gt;/&lt;pathtoyourapp&gt;/post/
 
    <?xml version="1.0" encoding="UTF-8"?>
    <created>true</created>
 
 - [PUT] http://&lt;yourhost&gt;/&lt;pathtoyourapp&gt;/post/1
 
    <?xml version="1.0" encoding="UTF-8"?>
    <edit>true</edit>

 - [DELETE] http://&lt;yourhost&gt;/&lt;pathtoyourapp&gt;/post/1

    <?xml version="1.0" encoding="UTF-8"?>
    <deleted>true</deleted>

Notice how each of the methods above are automatically routed to the correct action even if we don't specify it in the url as action.
All what we need to do is use the correct HTTP method, note that the action can still be specified if for example you want to use the POST for the view/index method as well.

Important Notice
================

When using parameters, by default CakePHP only does the routing with numbers.
You'll need to modify the default ResourceMapping and add a regex that answers to your needs.
See the CakePHP manual for more information about that