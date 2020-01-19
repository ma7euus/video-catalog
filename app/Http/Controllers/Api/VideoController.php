<?php

namespace App\Http\Controllers\Api;

use App\Models\Video;
use App\Rules\GenresHasCategoriesRule;
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
        'categories_id' => 'required|array|exists:categories,id,deleted_at,NULL',
        'genres_id' => [
            'required',
            'array',
            'exists:genres,id,deleted_at,NULL'
        ],
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
        $this->addRuleIfGenreHasCategories($this->request);
        return $this->validationRules;
    }

    protected function rulesUpdate() {
        $this->addRuleIfGenreHasCategories($this->request);
        return $this->validationRules;
    }

    protected function addRuleIfGenreHasCategories(Request $request) {
        $categoriesId = $request->get('categories_id');
        $categoriesId = is_array($categoriesId) ? $categoriesId : [];
        $this->validationRules['genres_id'][] = new GenresHasCategoriesRule($categoriesId);
    }

    protected function handleRelations(Model $model, Request $request) {
        $model->categories()->sync($request->get('categories_id'));
        $model->genres()->sync($request->get('genres_id'));
        return $model;
    }
}
