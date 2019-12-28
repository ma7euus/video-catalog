<?php

namespace App\Http\Controllers\Api;

use App\Models\Video;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class VideoController extends BasicCrudController {

    protected $validationRules = [
        'title' => 'required|max:255',
        'description' => 'required',
        'year_launched' => 'required|date_format:Y',
        'opened' => 'boolean',
        'opened' => 'boolean',
        'duration' => 'required|integer',
        'categories_id' => 'required|array|exists:categories,id',
        'genres_id' => 'required|array|exists:genres,id',
    ];

    public function __construct() {
        parent::__construct();
        $this->validationRules['rating'] = 'required|in:' . implode(',', Video::RATING_LIST);
    }

    /**
     * @return string
     */
    protected function model() {
        return Video::class;
    }

    protected function rulesStore() {
        return $this->validationRules;
    }

    protected function rulesUpdate() {
        return $this->validationRules;
    }

    protected function handleRelations(Model $model, Request $request) {
        $model->categories()->sync($request->get('categories_id'));
        $model->genres()->sync($request->get('genres_id'));
        return $model;
    }
}
