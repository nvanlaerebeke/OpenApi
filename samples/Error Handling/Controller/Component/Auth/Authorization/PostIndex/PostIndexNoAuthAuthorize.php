<?php
class PostIndexNoAuthAuthorize extends BaseAuthorize {
    public function authorize($user, CakeRequest $request) {
        return true;
    }
}