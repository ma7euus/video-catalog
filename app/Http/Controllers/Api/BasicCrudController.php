<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

abstract class BasicCrudController extends Controller {

    protected $validationRules = [];

    /**
     * @var Request
     */
    protected $request;

    public function __construct() {}

    abstract protected function model();

    abstract protected function rulesStore();

    abstract protected function rulesUpdate();

    protected function handleRelations(Model $model, Request $request){
        return $model;
    }

    public function index() {
        return $this->model()::all();
    }

    public function store(Request $request) {
        $this->request = $request;
        $validationData = $this->validate($request, $this->rulesStore());
        $obj = $this->handleStore($request, $validationData);
        $obj->refresh();
        return $obj;
    }

    /**
     * @param Request $request
     * @param $validationData
     * @return Model
     * @throws \Throwable
     */
    protected function handleStore(Request $request, $validationData) {
        /** @var Model $obj */
        $obj = \DB::transaction(function () use ($request, $validationData) {
            return $this->handleRelations($this->model()::create($validationData), $request);
        });
        return $obj;
    }

    protected function findOrFail($id) {
        /** @var Model $model */
        $model = $this->model();
        $keyName = (new $model)->getRouteKeyName();
        return $this->model()::where($keyName, $id)->firstOrFail();
    }

    public function show($id) {
        return $this->findOrFail($id);
    }

    public function update(Request $request, $id) {
        $obj = $this->findOrFail($id);
        $this->request = $request;
        $validationData = $this->validate($request, $this->rulesUpdate());
        $obj = $this->handleUpdate($request, $obj, $validationData);
        return $obj;
    }

    /**
     * @param Request $request
     * @param Model $obj
     * @param $validationData
     * @return Model
     * @throws \Throwable
     */
    protected function handleUpdate(Request $request, Model $obj, $validationData) {
        /** @var Model $obj */
        $obj = \DB::transaction(function () use ($request, $obj, $validationData) {
            $obj->update($validationData);
            return $this->handleRelations($obj, $request);
        });
        return $obj;
    }

    public function destroy($id) {
        $obj = $this->findOrFail($id);
        $obj->delete();
        return response()->noContent();
    }

}
