<?php

namespace Tests\Feature\Http\Controllers\Api;

use Illuminate\Validation\ValidationException;
use Tests\Stubs\Controllers\CategoryControllerStub;
use Tests\Stubs\Models\CategoryStub;
use Tests\TestCase;
use Illuminate\Http\Request;

class BasicCrudControllerTest extends TestCase {

    /**
     * @var CategoryControllerStub
     */
    protected $controller;

    protected function setUp(): void {
        parent::setUp();
        CategoryStub::dropTable();
        CategoryStub::createTable();
        $this->controller = new CategoryControllerStub();
    }

    protected function tearDown(): void {
        CategoryStub::dropTable();
        parent::tearDown();
    }

    public function testIndex() {
        /** @var CategoryStub $category */
        $category = CategoryStub::create(['name' => 'test', 'description' => 'test_description']);
        $this->assertEquals([$category->toArray()], $this->controller->index()->toArray());
    }

    public function testInvalidationDataInStore() {
        $this->expectException(ValidationException::class);
        $request = \Mockery::mock(Request::class);
        $request->shouldReceive('all')->once()->andReturn(['name' => '']);
        $this->controller->store($request);
    }

    public function testStore() {
        $request = \Mockery::mock(Request::class);
        $request->shouldReceive('all')
            ->once()
            ->andReturn(['name' => 'test', 'description' => 'test_description']);
        $obj = $this->controller->store($request);
        $this->assertEquals(
            CategoryStub::find(1)->toArray(),
            $obj->toArray()
        );
    }

}
