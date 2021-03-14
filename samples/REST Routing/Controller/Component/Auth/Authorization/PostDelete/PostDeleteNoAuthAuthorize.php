<?php
class PostDeleteNoAuthAuthorize extends BaseAuthorize {
    public function authorize($user, CakeRequest $request) {
        return true;
    }
}