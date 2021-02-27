<?php

namespace App\Observers;

use Bschmitt\Amqp\Message;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SyncModelObserver {

    public function created(Model $model) {
        $modelName = $this->getModelName($model);
        $data = $model->toArray();
        $action = __FUNCTION__;
        try {
            $this->publish("model.{$modelName}.{$action}", $data);
        } catch (\Exception $e) {
            $this->reportException([$modelName, $model, $action, $e]);
        }
    }

    public function updated(Model $model) {
        $modelName = $this->getModelName($model);
        $data = $model->toArray();
        $action = __FUNCTION__;
        try {
            $this->publish("model.{$modelName}.{$action}", $data);
        } catch (\Exception $e) {
            $this->reportException([$modelName, $model, $action, $e]);
        }
    }

    public function deleted(Model $model) {
        $modelName = $this->getModelName($model);
        $data = ['id' => $model->id];
        $action = __FUNCTION__;
        try {
            $this->publish("model.{$modelName}.{$action}", $data);
        } catch (\Exception $e) {
            $this->reportException([$modelName, $model, $action, $e]);
        }
    }

    protected function getModelName(Model $model) {
        $shortName = (new \ReflectionClass($model))->getShortName();
        return Str::snake($shortName);
    }

    protected function publish($routingKey, array $data) {
        $message = new Message(json_encode($data), [
            'content_type' => 'application/json',
            'delivery_mode' => 2
        ]);
        \Amqp::publish($routingKey, $message, [
            'exchange_type' => 'topic',
            'exchange' => 'amq.topic'
        ]);
    }

    protected function reportException($params) {
        list($modelName, $model, $action, $e) = $params;
        $myException = new \Exception("The model {$modelName} with {$model->id} not synced on {$action}", 0, $e);
        report($myException);
    }

    public function belongsToManyAttached($relation, $model, $ids) {
        $this->belongsToManyAction(array_merge(['attached'], func_get_args()));
    }

    public function belongsToManyDetached($relation, $model, $ids) {
        $this->belongsToManyAction(array_merge(['attached'], func_get_args()));
    }

    protected function belongsToManyAction() {
        list($action, $relation, $model, $ids) = current(func_get_args());
        $modelName = $this->getModelName($model);
        $relationName = Str::snake($relation);
        $data = [
            'id' => $model->id,
            'relation_ids' => $ids,
        ];
        try {
            $this->publish("model.{$modelName}_{$relationName}.{$action}", $data);
        } catch (\Exception $e) {
            $this->reportException([$modelName, $model, $action, $e]);
        }
    }

    public function restored(Model $model) {
        //
    }

    public function forceDeleted(Model $model) {
        //
    }
}
