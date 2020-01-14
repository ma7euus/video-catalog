<?php

namespace Tests\Traits;

trait TestRelations {


    protected function syncRelations(array $sendData) {

        foreach ($this->relationTables() as $table) {
            $ids = factory($table['relation_model'], 3)->create()->pluck('id')->toArray();

            $sendData[$table['main_table_key_relation']] = [];
            $sendData[$table['main_table_key_relation']][] = $ids[0];
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
