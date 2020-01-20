<?php

namespace Tests\Traits;

use function Psy\debug;

trait TestRelations {


    protected function syncRelations(array $sendData) {

        foreach ($this->relationTables() as $table) {
            $ids = factory($table['relation_model'], 3)->create()->pluck('id')->toArray();

            $sendData[$table['main_table_key_relation']] = [];
            $sendData[$table['main_table_key_relation']][] = $ids[0];
            if (isset($table['exec_func_1'])) {
                //dd($table['relation_model']::find($ids[0])->{$table['exec_func_1']});
                $keyName = (new $table['relation_model'])->getRouteKeyName();
                $obj = $table['relation_model']::where($keyName, $ids[0])->firstOrFail();
                $obj->{$table['exec_func_1']['name']}()->sync($sendData[$table['exec_func_1']['sendData_arg']]);
            }
            $response = $this->json('POST', $this->routeStore(), $sendData);
            $this->assertDatabaseHas($table['table'], [
                $table['main_key'] => $response->json('id'),
                $table['relation_key'] => $ids[0]
            ]);

            $sendData[$table['main_table_key_relation']] = [$ids[1], $ids[2]];

            if (isset($table['exec_func_1'])) {
                //dd($table['relation_model']::find($ids[0])->{$table['exec_func_1']});
                $keyName = (new $table['relation_model'])->getRouteKeyName();
                $obj = $table['relation_model']::where($keyName, $ids[1])->firstOrFail();
                $obj->{$table['exec_func_1']['name']}()->sync($sendData[$table['exec_func_1']['sendData_arg']]);
                $obj = $table['relation_model']::where($keyName, $ids[2])->firstOrFail();
                $obj->{$table['exec_func_1']['name']}()->sync($sendData[$table['exec_func_1']['sendData_arg']]);
            }

            $response = $this->json('PUT', $this->routeUpdate($response->json('id')), $sendData);

            $this->assertDatabaseMissing($table['table'], [
                $table['main_key'] => $response->json('id'),
                $table['relation_key'] => $ids[0]
            ]);

            $this->assertDatabaseHas($table['table'], [
                $table['main_key'] => $response->json('id'),
                $table['relation_key'] => $ids[1]
            ]);

            $this->assertDatabaseHas($table['table'], [
                $table['main_key'] => $response->json('id'),
                $table['relation_key'] => $ids[2]
            ]);
        }
    }

}
