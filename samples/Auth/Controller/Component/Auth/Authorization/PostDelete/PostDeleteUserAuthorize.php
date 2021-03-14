<?php
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