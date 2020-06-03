<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\VideoResource;
use App\Models\Video;
use App\Rules\GenresHasCategoriesRule;
use Illuminate\Database\Eloquent\Builder;
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
        'cast_members_id' => [
            'required',
            'array',
            'exists:cast_members,id,deleted_at,NULL'
        ],
        'video_file' => 'sometimes|mimetypes:video/mp4|max:' . Video::MAX_VIDEO_SIZE,
        'thumb_file' => 'sometimes|image|max:' . Video::MAX_THUMB_SIZE,
        'trailer_file' => 'sometimes|mimetypes:video/mp4|max:' . Video::MAX_TRAILER_SIZE,
        'banner_file' => 'sometimes|image|max:' . Video::MAX_BANNER_SIZE
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
     * @return Model
     */
    protected function handleUpdate(Request $request, Model $obj, $validationData) {
        $obj->update($validationData);
        return $obj;
    }

    /**
     * @return array
     */
    protected function rulesStore() {
        $this->addRuleIfGenreHasCategories($this->request);
        return $this->validationRules;
    }

    /**
     * @return array
     */
    protected function rulesUpdate() {
        $this->addRuleIfGenreHasCategories($this->request);
        return $this->validationRules;
    }

    /**
     * @param Request $request
     */
    protected function addRuleIfGenreHasCategories(Request $request) {
        $categoriesId = $request->get('categories_id');
        $categoriesId = is_array($categoriesId) ? $categoriesId : [];
        $idx = count($this->validationRules['genres_id']) - 1;
        $this->validationRules['genres_id'][$idx] = new GenresHasCategoriesRule($categoriesId);
    }

    protected function resource() {
        return VideoResource::class;
    }

    protected function queryBuilder(): Builder {
        return parent::queryBuilder()->with(['genres.categories']);
    }
}
