<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Database\Eloquent\Builder;

abstract class BasicCrudController extends Controller {

    protected $validationRules = [];

    protected $defaultPerPage = 15;

    /**
     * @var Request
     */
    protected $request;

    public function __construct() {
    }

    abstract protected function model();

    abstract protected function rulesStore();

    abstract protected function rulesUpdate();

    protected abstract function resource();

    protected function handleRelations(Model $model, Request $request) {
        return $model;
    }

    protected function resourceCollection() {
        return $this->resource();
    }

    public function index(Request $request) {
        $perPage = (int)$request->get('per_page', $this->defaultPerPage);
        $hasFilter = in_array(Filterable::class, class_uses($this->model()));

        $query = $this->queryBuilder();

        if ($hasFilter) {
            $query = $query->filter($request->all());
        }

        $data = $request->has('all') || !$this->defaultPerPage ? $query->get() : $query->paginate($perPage);

        $resourceCollectionClass = $this->resourceCollection();
        $ref = new \ReflectionClass($this->resourceCollection());
        return $ref->isSubclassOf(ResourceCollection::class)
            ? new $resourceCollectionClass($data)
            : $resourceCollectionClass::collection($data);
    }

    public function store(Request $request) {
        $this->request = $request;
        $validationData = $this->validate($request, $this->rulesStore());
        $obj = $this->handleStore($request, $validationData);
        $obj->refresh();
        return $this->handleResponse($obj);
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
            return $this->handleRelations($this->queryBuilder()->create($validationData), $request);
        });
        return $obj;
    }

    protected function findOrFail($id) {
        /** @var Model $model */
        $model = $this->model();
        $keyName = (new $model)->getRouteKeyName();
        return $this->queryBuilder()->where($keyName, $id)->firstOrFail();
    }

    /**
     * @param $obj
     * @return mixed
     */
    protected function handleResponse($obj) {
        $resource = $this->resource();
        return new $resource($obj);
    }

    public function show($id) {
        $obj = $this->findOrFail($id);
        return $this->handleResponse($obj);
    }

    public function update(Request $request, $id) {
        $obj = $this->findOrFail($id);
        $this->request = $request;
        $validationData = $this->validate($request, $this->rulesUpdate());
        $obj = $this->handleUpdate($request, $obj, $validationData);
        return $this->handleResponse($obj);
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

    protected function queryBuilder(): Builder {
        return $this->model()::query();
    }
}
