<?php
class ProductAddUserAuthorize extends BaseAuthorize {
    public function authorize($user, CakeRequest $request) {
        if($user['User']['allowadd']) {
            return true;
        }
        return false;
    }
}