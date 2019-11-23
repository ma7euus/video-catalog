<?php

namespace App\Http\Controllers\Api;

use App\Models\CastMember;

class CastMemberController extends BasicCrudController {

    protected $validationRules = [
        'name' => 'required|max:255'
    ];

    public function __construct() {
        $this->validationRules['type'] = 'required|in:' . implode(',', [CastMember::TYPE_DIRECTOR, CastMember::TYPE_ACTOR]);
    }

    /**
     * @return CastMember
     */
    protected function model() {
        return CastMember::class;
    }

    protected function rulesStore() {
        return $this->validationRules;
    }

    protected function rulesUpdate() {
        return $this->validationRules;
    }
}