<?php
class ProductAddMachineAuthorize extends BaseAuthorize {
    public function authorize($user, CakeRequest $request) {
        return true;
    }
}