<?php

namespace Tests\Traits;

use Illuminate\Database\Eloquent\Model;

trait TestController {

    /**
     * @return Model
     */
    abstract protected function model();

    /**
     * @return string
     */
    abstract protected function routeStore();

    /**
     * @return string
     */
    abstract protected function routeUpdate();

    /**
     * @return mixed
     */
    abstract protected function withRelations();
}
