<?php
class ProductDeleteUserAuthorize extends BaseAuthorize {
    public function authorize($user, CakeRequest $request) {
        if($user['User']['allowdelete']) {
            return true;
        }
        return false;
    }
}