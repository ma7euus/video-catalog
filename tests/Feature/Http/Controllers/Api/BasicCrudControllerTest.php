<?php

namespace Tests\Feature\Http\Controllers\Api;

use Tests\Stubs\Controllers\CategoryControllerStub;
use Tests\Stubs\Models\CategoryStub;
use Tests\TestCase;

class BasicCrudControllerTest extends TestCase {

    protected function setUp(): void {
        parent::setUp();
        CategoryStub::dropTable();
        CategoryStub::createTable();
    }

    protected function tearDown(): void {
        CategoryStub::dropTable();
        parent::tearDown();
    }

    public function testIndex() {
        /** @var CategoryStub $category */
        $category = CategoryStub::create(['name' => 'test', 'description' => 'test_description']);
        $controller = new CategoryControllerStub();
        $this->assertEquals([$category->toArray()], $controller->index()->toArray());
    }
}
