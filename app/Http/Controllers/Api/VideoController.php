<?php

namespace App\Http\Controllers\Api;

use App\Models\Video;

class VideoController extends BasicCrudController {

    protected $validationRules = [
        'title' => 'required|max:255',
        'description' => 'required',
        'year_launched' => 'required|date_format:Y',
        'opened' => 'boolean',
        'duration' => 'required|integer',
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
}
