<?php

namespace App\Http\Controllers\Api;

use App\Models\Genre;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class GenreController extends BasicCrudController {

    protected $validationRules = [
        'name' => 'required|max:255',
        'is_active' => 'boolean',
        'categories_id' => 'required|array|exists:categories,id,deleted_at,NULL'
    ];

    public function __construct() {
        parent::__construct();
    }

    /**
     * @return Genre
     */
    protected function model() {
        return Genre::class;
    }

    protected function rulesStore() {
        return $this->validationRules;
    }

    protected function rulesUpdate() {
        return $this->validationRules;
    }

    protected function handleRelations(Model $model, Request $request) {
        $model->categories()->sync($request->get('categories_id'));
        return $model;
    }
}
