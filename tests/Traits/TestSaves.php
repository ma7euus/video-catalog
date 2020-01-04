<?php


namespace Tests\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\TestResponse;

trait TestSaves {

    /**
     * @param array $sendData
     * @param array $testDatabase
     * @param array|null $testJsonData
     * @return TestResponse
     * @throws \Exception
     */
    protected function assertStore(array $sendData, array $testDatabase, array $testJsonData = null) {
        /** @var TestResponse $response */
        $response = $this->json('POST', $this->routeStore(), $sendData);
        if ($response->status() !== 201) {
            throw new \Exception("Response status must be 201, given {$response->status()}:\n{$response->content()}");
        }
        $testDatabase = array_diff_key($testDatabase, $this->withRelations());
        $this->assertInDatabase($response, $testDatabase);
        $this->assertJsonResponseContent($response, $testDatabase, $testJsonData);
        return $response;
    }

    protected function assertUpdate(array $sendData, array $testDatabase, array $testJsonData = null) {
        /** @var TestResponse $response */
        $response = $this->json('PUT', $this->routeUpdate(), $sendData);
        if ($response->status() !== 200) {
            throw new \Exception("Response status must be 200, given {$response->status()}:\n{$response->content()}");

        }
        $testDatabase = array_diff_key($testDatabase, $this->withRelations());
        $this->assertInDatabase($response, $testDatabase);
        $this->assertJsonResponseContent($response, $testDatabase, $testJsonData);
        return $response;
    }

    private function assertInDatabase($response, $testDatabase) {
        /** @var Model $model */
        $model = $this->model();
        $table = (new $model)->getTable();
        $this->assertDatabaseHas($table, $testDatabase + ['id' => $response->json(['id'])]);
    }

    private function assertJsonResponseContent($response, $testDatabase, $testJsonData = null) {
        $testResponse = $testJsonData ?? $testDatabase;
        $response->assertJsonFragment($testResponse + ['id' => $response->json(['id'])]);
    }

}
