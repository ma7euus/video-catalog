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
     * @param Request $request
     * @param $validationData
     * @return Model|mixed
     */
    protected function handleStore(Request $request, $validationData) {
        return $this->model()::create($validationData);
    }

    /**
     * @param Request $request
     * @param Model $obj
     * @param $validationData
     * @return bool|Model
     */
    protected function handleUpdate(Request $request, Model $obj, $validationData) {
        return $obj->update($validationData);
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
        $idx = count($this->validationRules['genres_id']) - 1;
        $this->validationRules['genres_id'][$idx] = new GenresHasCategoriesRule($categoriesId);
    }
}
