<?php
class ProductEditNoAuthAuthorize extends BaseAuthorize {
    public function authorize($user, CakeRequest $request) {
        return true;
    }
}