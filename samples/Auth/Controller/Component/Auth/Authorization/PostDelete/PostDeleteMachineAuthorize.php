<?php
class PostDeleteMachineAuthorize extends BaseAuthorize {
    public function authorize($user, CakeRequest $request) {
        return true;
    }
}