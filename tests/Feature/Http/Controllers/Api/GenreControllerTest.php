<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\GenreStub;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\TestController;
use Tests\Traits\TestValidations;

class GenreControllerTest extends TestCase {

    use DatabaseMigrations, TestController, TestValidations;

    /**
     * @var GenreStub
     */
    private $genre;

    protected function setUp(): void {
        parent::setUp();
        $this->genre = factory(GenreStub::class)->create();
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testIndex() {
        $response = $this->get(route('genres.index'));

        $response->assertStatus(200)
            ->assertJson([$this->genre->toArray()]);
    }

    public function testShow() {
        $response = $this->get(route('genres.show', ['genre' => $this->genre->id]));

        $response->assertStatus(200)
            ->assertJson($this->genre->toArray());
    }

    public function testInvalidationData() {

        $data = [
            'name' => ''
        ];
        $this->assertInvalidationInStoreAction($data, 'required');
        $this->assertInvalidationInUpdateAction($data, 'required');

        $data = [
            'name' => str_repeat('a', 256),
        ];
        $this->assertInvalidationInStoreAction($data, 'max.string', ['max' => 255]);
        $this->assertInvalidationInUpdateAction($data, 'max.string', ['max' => 255]);

        $data = [
            'is_active' => 'a'
        ];
        $this->assertInvalidationInStoreAction($data, 'boolean');
        $this->assertInvalidationInUpdateAction($data, 'boolean');
    }

    public function testStore() {
        $response = $this->json('POST', route('genres.store'), [
            'name' => 'test'
        ]);

        $id = $response->json('id');
        $genre = GenreStub::find($id);

        $response->assertStatus(201)
            ->assertJson($genre->toArray());

        $this->assertTrue($response->json('is_active'));

        $response = $this->json(
            'POST',
            route('genres.store'),
            [
                'name' => 'test',
                'is_active' => false
            ]
        );

        $response->assertJsonFragment([
            'is_active' => false
        ]);

    }

    public function testUpdate() {

        $genre = factory(GenreStub::class)->create([
            'is_active' => false
        ]);
        $response = $this->json('PUT', route('genres.update', ['genre' => $genre->id]), [
            'name' => 'test',
            'is_active' => true
        ]);

        $id = $response->json('id');
        $genre = GenreStub::find($id);

        $response->assertStatus(200)
            ->assertJson($genre->toArray())
            ->assertJsonFragment([
                'is_active' => true
            ]);
    }

    public function testDestroy() {
        $response = $this->json('DELETE', route('genres.destroy', ['genre' => $this->genre->id]));
        $response->assertStatus(204);
        $this->assertNull(GenreStub::find($this->genre->id));
        $this->assertNotNull(GenreStub::withTrashed()->find($this->genre->id));
    }

    /**
     * @return string
     */
    protected function routeStore() {
        return route('genres.store');
    }

    /**
     * @return string
     */
    protected function routeUpdate() {
        return route('genres.update', ['genre' => $this->genre->id]);
    }

    /**
     * @return GenreStub
     */
    protected function model() {
        return get_class($this->genre);
    }
}
