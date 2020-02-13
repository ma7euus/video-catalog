<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Controllers\Api\BasicCrudController;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
        $category = CategoryStub::create(['name' => 'name_test', 'description' => 'description_test']);
        $this->assertEquals([$category->toArray()], $this->controller->index(new Request())->response()->getData(true)['data']);
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
            $obj->response()->getData(true)['data']
        );
    }

    public function testIfFindOrFailFetchModel() {
        /** @var CategoryStub $category */
        $category = CategoryStub::create(['name' => 'test', 'description' => 'test_description']);

        $reflectionClass = new \ReflectionClass(BasicCrudController::class);
        $reflectionMethod = $reflectionClass->getMethod('findOrFail');
        $reflectionMethod->setAccessible(true);

        $result = $reflectionMethod->invokeArgs($this->controller, [$category->id]);
        $this->assertInstanceOf(CategoryStub::class, $result);
    }

    public function testIfFindOrFailThrowExceptionWhenIdInvalid() {
        $this->expectException(ModelNotFoundException::class);
        $reflectionClass = new \ReflectionClass(BasicCrudController::class);
        $reflectionMethod = $reflectionClass->getMethod('findOrFail');
        $reflectionMethod->setAccessible(true);

        $result = $reflectionMethod->invokeArgs($this->controller, [0]);
    }

    public function testShow() {
        /** @var CategoryStub $category */
        $category = CategoryStub::create(['name' => 'test', 'description' => 'test_description']);
        $result = $this->controller->show($category->id);
        $this->assertEquals($result->response()->getData(true)['data'], CategoryStub::find(1)->toArray());
    }

    public function testUpdate() {
        $category = CategoryStub::create(['name' => 'test', 'description' => 'test_description']);
        $request = \Mockery::mock(Request::class);
        $request->shouldReceive('all')
            ->once()
            ->andReturn(['name' => 'test_alterado', 'description' => 'test_description_alterado']);
        $obj = $this->controller->update($request, $category->id);
        $this->assertEquals(
            CategoryStub::find(1)->toArray(),
            $obj->response()->getData(true)['data']
        );
    }

    public function testDestroy() {
        $category = CategoryStub::create(['name' => 'test', 'description' => 'test_description']);
        $response = $this->controller->destroy($category->id);
        $this->createTestResponse($response)
            ->assertStatus(204);
        $this->assertCount(0, CategoryStub::all());
    }
}
