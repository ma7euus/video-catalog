<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class CategoryController extends BasicCrudController {

    protected $validationRules = [
        'name' => 'required|max:255',
        'description' => 'nullable',
        'is_active' => 'boolean',
    ];

    public function __construct() {
        parent::__construct();
    }

    /**
     * @return Category
     */
    protected function model() {
        return Category::class;
    }

    protected function rulesStore() {
        return $this->validationRules;
    }

    protected function rulesUpdate() {
        return $this->validationRules;
    }

    protected function resource() {
        return CategoryResource::class;
    }
}
