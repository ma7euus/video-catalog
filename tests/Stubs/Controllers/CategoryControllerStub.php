<?php

namespace Tests\Stubs\Controllers;

use App\Http\Controllers\Api\BasicCrudController;
use Illuminate\Database\Eloquent\Model;
use Tests\Stubs\Models\CategoryStub;

class CategoryControllerStub extends BasicCrudController {

    /**
     * @return Model
     */
    protected function model() {
        return CategoryStub::class;
    }

    /**
     * @return array
     */
    protected function rulesStore() {
        return [
            'name' => 'required|max:255',
            'description' => 'nullable'
        ];
    }
}
