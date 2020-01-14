<?php

namespace Tests\Traits;

use Illuminate\Database\Eloquent\Model;

trait TestController {

    /**
     * @return Model
     */
    abstract protected function model();

    /**
     * @param null $id
     * @return mixed
     */
    abstract protected function routeStore($id = null);

    /**
     * @return string
     */
    abstract protected function routeUpdate();

}
