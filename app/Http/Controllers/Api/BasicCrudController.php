<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

abstract class BasicCrudController extends Controller {

    protected $validationRules = [];

    abstract protected function model();

    abstract protected function rulesStore();

    abstract protected function rulesUpdate();

    public function __construct() {}

    public function index() {
        return $this->model()::all();
    }

    public function store(Request $request) {
        $validationData = $this->validate($request, $this->rulesStore());
        /** @var Model $obj */
        $obj = $this->model()::create($validationData);
        $obj->refresh();
        return $obj;
    }

    protected function findOrFail($id) {
        /** @var Model $model */
        $model = $this->model();
        $keyName = (new $model)->getRouteKeyName();
        return $this->model()::where($keyName, $id)->firstOrFail();
    }

    public function show($id) {
        $obj = $this->findOrFail($id);
        return $obj;
    }

    public function update(Request $request, $id) {
        $obj = $this->findOrFail($id);
        $validationData = $this->validate($request, $this->rulesUpdate());
        $obj->update($validationData);
        return $obj;
    }

    public function destroy($id) {
        $obj = $this->findOrFail($id);
        $obj->delete();
        return response()->noContent();
    }

}
