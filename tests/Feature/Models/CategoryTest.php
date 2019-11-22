<?php

namespace Tests\Feature\Models;

use App\Models\CategoryStub;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryTest extends TestCase {

    use DatabaseMigrations;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testList() {
        factory(CategoryStub::class, 1)->create();
        $categories = CategoryStub::all();
        $this->assertCount(1, $categories);
        $categoryKey = array_keys($categories->first()->getAttributes());
        $this->assertEqualsCanonicalizing([
            "id",
            "name",
            "is_active",
            "description",
            "created_at",
            "updated_at",
            "deleted_at"
        ],
            $categoryKey);
    }

    public function testCreate() {
        $category = CategoryStub::create([
            'name' => 'test1'
        ]);
        $category->refresh();

        $this->assertEquals(36, strlen($category->id));
        $this->assertEquals('test1', $category->name);
        $this->assertNull($category->description);
        $this->assertTrue($category->is_active);

        $category = CategoryStub::create([
            'name' => 'test1',
            'description' => null
        ]);
        $this->assertNull($category->description);

        $category = CategoryStub::create([
            'name' => 'test1',
            'description' => 'test_description'
        ]);
        $this->assertEquals('test_description', $category->description);

        $category = CategoryStub::create([
            'name' => 'test1',
            'is_active' => false
        ]);
        $this->assertFalse($category->is_active);

        $category = CategoryStub::create([
            'name' => 'test1',
            'is_active' => true
        ]);
        $this->assertTrue($category->is_active);
    }

    public function testUpdate() {
        /** @var CategoryStub $category */
        $category = factory(CategoryStub::class)->create([
            'description' => 'test_description',
            'is_active' => false
        ])->first();

        $data = [
            'name' => 'test_name_updated',
            'description' => 'test_description_updated',
            'is_active' => true,
        ];
        $category->update($data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $category->{$key});
        }
    }

    public function testDelete() {
        /**@var CategoryStub $category */
        $category = factory(CategoryStub::class)->create();
        $category->delete();
        $this->assertNull(CategoryStub::find($category->id));

        $category->restore();
        $this->assertNotNull(CategoryStub::find($category->id));
    }
}
