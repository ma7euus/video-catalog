<?php

namespace Tests\Traits;

use function Psy\debug;

trait TestRelations {


    protected function syncRelations(array $testData) {

        foreach ($this->relationTables() as $table) {
            $sendData = $testData;
            $ids = factory($table['relation_model'], 3)->create()->pluck('id')->toArray();

            foreach ($ids as $id) {
                foreach ($table['pivot'] as $func => $field) {
                    $keyName = (new $table['relation_model'])->getRouteKeyName();
                    $obj = $table['relation_model']::where($keyName, $id)->firstOrFail();
                    $obj->{$func}()->sync($sendData[$field]);
                }
            }

            $sendData[$table['main_table_key_relation']] = [$ids[0]];
            $response = $this->json('POST', $this->routeStore(), $sendData);
            $this->assertDatabaseHas($table['table'], [
                $table['main_key'] => $response->json('id'),
                $table['relation_key'] => $ids[0]
            ]);

            $sendData[$table['main_table_key_relation']] = [$ids[1], $ids[2]];
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
