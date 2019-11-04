<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Lang;
use Tests\TestCase;

class CategoryControllerTest extends TestCase {

    use DatabaseMigrations;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testIndex() {
        /** @var Category $category */
        $category = factory(Category::class)->create();
        $response = $this->get(route('categories.index'));

        $response->assertStatus(200)
            ->assertJson([$category->toArray()]);
    }

    public function testShow() {
        /** @var Category $category */
        $category = factory(Category::class)->create();
        $response = $this->get(route('categories.show', ['category' => $category->id]));

        $response->assertStatus(200)
            ->assertJson($category->toArray());
    }

    public function testInvalidationData() {

        $response = $this->json('POST', route('categories.store', []));
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonFragment([
                \Lang::get('validation.required', ['attribute' => 'name'])
            ]);
    }

}
