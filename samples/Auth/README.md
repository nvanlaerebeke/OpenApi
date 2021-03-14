OpenApi-Samples: Auth Sample
=============================

Introduction
============

The Auth process always consists of 2 steps:
 - Authentication: identifies who you are
 - Authorization: checks if you're allowed to run the action
 

OpenApi makes this process real easy and scalable.

In most API samples you'll find on the internet, this is the point they all fall off, in larger API's where have a multitude of ways to authenticate & authorize your controllers becomes unmanageable and polluted.

OpenApi solves this in a few ways:
 - Prevent controller pollution by splitting off the authentication and authorization into separate classes (components)
 - The ability to use multiple authentication AND authorization methods for a single action without writing to much code

Configuration
==============

Auth has only 1 config setting, one that allows you to have the authorization be done by a certain authorization context.

    Configure::write('OpenApi.SeparateAuthorization', true);

This comes in handy when we have an API call that must be accessable with multiple authentication methods.<br />
We'll come back to that later


Configuration for the api calls is done in your controllers, by specifying an $AuthConfig configuration array as class variable, an example from in the Basic Sample:

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
    
This array says that for the create action 'NoAuth' authentication is done, this stands for 'No Authentication Required' - meaning we do not care who you are.<br />
What is also specified here is that for this authentication method you need the 'FirstName', 'Email' and 'Age' parameter.<br />
Additionally you can even specify validation rules for the parameters, see the Validation sample for more info.

Authentication
==============

CakePHP and OpenApi come with a few build in Authentication methods, in the cake framework you'll find:
 - FormAuthenticate
 - BasicAuthenticate
 - DigestAuthenticate
 
OpenApi comes with:
 - NoAuthAuthenticate
 - IPAddressAuthenticate 
 
Authorization
==============

An example of this would be a 'PostController' delete method that must be accessible using basic authentication, using a username/password and at the same time by a certain server IP Address.

This would mean you have 3 auth classes, one for Basic Authentication, one for the Username and Password and another one for checking IP Addresses.


You can divide these in 2 groups:
 - User access: Basic Authentication and Certificate will return a User
 - Server access: IP Address, this one doesn't have a 'User' context, we just know the IP Address

The trick to accomplish this lies in the Authentication process.<br />
It's known in what 'context' the request is done when authenticating.

Internally this is called the 'authorizetype', that authtype is then used to call the specific Authorize class.<br />
The authorization class that will be called will be '<Controller><Action><AuthorizeType>Authorize'.<br />
The classes must be located in app/Controller/Component/Auth/Authorize/<Controller><Action>/

Example authorize classes: 
  - PostEditUserAuthorize in: app/Controller/Component/Auth/Authorize/PostEdit/
  - PostDeleteServerAuthorize in: app/Controller/Component/Auth/Authorize/PostDelete/

When the "SeparateAuthorization" setting this to false, the authorize classes aren’t divided in sub directories and are located in the app/Controller/Component/Auth/Authorize directory directly. 
 Going from the examples above:
  - PostEditAuthorize in: app/Controller/Component/Auth/Authorize/
  - PostDeleteAuthorize in: app/Controller/Component/Auth/Authorize/
  

Authentication Sample
===================== 

In the other samples we're using the 'NoAuth' Authenticate method, but for this one we'll demonstrate how to use the IPAddress and create a custom EmailPassword authentication class.

First off, the controller:

    class PostController extends AppController {
        /**
         * Auth configuration
         */
        public $AuthConfig = array(
            'index' => array(
                'NoAuth' => array()
            ),
            'delete' => array(
                'IPAddress' => array(),
                'EmailPassword' => array()
            )
        );
    
        public function index() {
            $this->set('response', array(
                'Post' => array(
                    'id' => '1',
                    'author' => 'SomeGuy',
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
    
The above controller says that everyone is allowed to go to the "Index" action and that to delete a post you need to authenticate using an IPAddress or an EmailPassword combination.<br />
In case of multiple authentication methods the order is top to bottom.

The IPAddress authenticate class we're using is configured by providing the IP's in your core.php

    Configure::write('OpenApi.Authentication.AllowedIPs', array(
       '192.168.1.2'
    ));  

When the IP does not match, it tries our custom EmailPassword Authenticate class, priority goes from top to bottom:

app/Controller/Component/Auth/EmailPasswordAuthenticate.php:

    App::uses('ApiBaseAuthenticate', 'Controller/Component/Auth');
    class EmailPasswordAuthenticate extends ApiBaseAuthenticate {
        private $user = null;

        public function authenticate(CakeRequest $request, CakeResponse $response) {
            //these are passed using GET, in reallity better use post over SSL or implement a layer before the CakeRequest is made so that all input is normalized
            $email = $request->query['emailaddress'];
            $pw = $request->query['password'];
            
            //in reality this will most likely be a DB query
            $this->user = array(
                'User' => array(
                    'id' => 1,
                    'emailaddress' => 'email@example.com',
                    'password' => 'mypassword'
                )
            );
            
            //simple basic compare
            if($email != $this->user['User']['emailaddress'] || $this->user['User']['password'] != $pw) {
                return false;
            }
    
            //Say we're using a 'User' authorize type when using EmailPassword and pass the         
            return array(
                'User' => $this->user['User'],
                'authorizetype' => 'User'
            );
        }
        
        public function getUser(CakeRequest $request){ return $this->user; }
    }
    
Once this passes the Authentication process, we need our 2 Authorize classes, 1 for when we authenticate machines, like the IPAddress Authenticate class and 1 for authenticating users like our custom 'EmailPassword' authenticate class.


    class PostDeleteMachineAuthorize extends BaseAuthorize {
        public function authorize($user, CakeRequest $request) {
            return true;
        }
    }
    
    class PostDeleteUserAuthorize extends BaseAuthorize {
        public function authorize($user, CakeRequest $request) {
            //in practice this will most likely be a DB query
            $post = array(
                'Post' => array(
                    'id' => 1,
                    'author' => 'SomeGuy',
                    'userid' => 1,
                    'email' => 'email@example.com',
                    'messsage' => 'MyMessage',
                    'date' => date('Y-m-d h:i')
                )
            );
            
            if($user['User']['id'] != $post['Post']['userid']) {
                return false;
            }
            return true;
        }
    }
    
When using the machine authorize class no extra validation is required, machines from the ip's provided in the core.php are allowed to delete any post.<br />
Using the EmailPassword authenticate method, we're in a 'User' context, and users need extra validation, only the person that made the post is allowed to delete it.

The result
==========

Point your browser to the following url's WITH the correct HTTP method:
 - [GET] http://&lt;yourhost&gt;/&lt;pathtoyourapp&gt;/post/
 - [DELETE] http://&lt;yourhost&gt;/&lt;pathtoyourapp&gt;/post/1/?emailaddress=email@example.com&password=mypassword
 
