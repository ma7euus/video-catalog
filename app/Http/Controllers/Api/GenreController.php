<?php

namespace App\Http\Controllers\Api;

use App\Models\Genre;

class GenreController extends BasicCrudController {

    protected $validationRules = [
        'name' => 'required|max:255',
        'is_active' => 'boolean'
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
}
