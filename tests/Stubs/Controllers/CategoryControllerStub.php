<?php

namespace Tests\Stubs\Controllers;

use App\Http\Controllers\Api\BasicCrudController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Tests\Stubs\Models\CategoryStub;

class CategoryControllerStub extends BasicCrudController {

    protected $validationRules = [
        'name' => 'required|max:255',
        'description' => 'nullable'
    ];

    /**
     * @return CategoryStub
     */
    protected function model() {
        return CategoryStub::class;
    }

    /**
     * @return array
     */
    protected function rulesStore() {
        return $this->validationRules;
    }

    protected function rulesUpdate() {
        return $this->validationRules;
    }

    protected function afterSave(Model $model, Request $request) {
        return $model;
    }
}
