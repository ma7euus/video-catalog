<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Controllers\Api\GenreController;
use App\Http\Resources\GenreResource;
use App\Models\Category;
use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\Request;
use Tests\Exceptions\TestExceptions;
use Tests\TestCase;
use Tests\Traits\TestController;
use Tests\Traits\TestRelations;
use Tests\Traits\TestResources;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class GenreControllerTest extends TestCase {

    use DatabaseMigrations, TestController, TestValidations, TestSaves, TestRelations, TestResources;

    /**
     * @var Genre
     */
    private $genre;
    private $serializedFields = [
        'id',
        'name',
        'is_active',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * @var array
     */
    private $sendData;

    protected function setUp(): void {
        parent::setUp();
        $this->genre = factory(Genre::class)->create([
            'is_active' => true
        ]);

        $category = factory(Category::class)->create();
        $this->sendData = [
            'categories_id' => [$category->id]
        ];
    }

    public function testIndex() {
        $response = $this->get(route('genres.index'));

        $response
            ->assertStatus(200)
            ->assertJson([
                'meta' => ['per_page' => 15]
            ])
            ->assertJsonStructure([
                'data' => [
                    '*' => $this->serializedFields
                ],
                'links' => [],
                'meta' => []
            ]);

        $resource = GenreResource::collection(collect([$this->genre]));
        $this->assertResource($response, $resource);
    }

    public function testShow() {
        $response = $this->get(route('genres.show', ['genre' => $this->genre->id]));
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => $this->serializedFields + ['categories' => []]
            ]);

        $id = $response->json('data.id');
        $resource = new GenreResource(Genre::find($id));
        $this->assertResource($response, $resource);
    }

    public function testInvalidationData() {

        $data = [
            'name' => '',
            'categories_id' => ''
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

        $data = [
            'categories_id' => 'a'
        ];
        $this->assertInvalidationInStoreAction($data, 'array');
        $this->assertInvalidationInUpdateAction($data, 'array');

        $data = [
            'categories_id' => [100]
        ];
        $this->assertInvalidationInStoreAction($data, 'exists');
        $this->assertInvalidationInUpdateAction($data, 'exists');

        /** @var Category $category */
        $category = factory(Category::class)->create();
        $category->delete();
        $data = [
            'categories_id' => [$category->id]
        ];
        $this->assertInvalidationInStoreAction($data, 'exists');
        $this->assertInvalidationInUpdateAction($data, 'exists');
    }

    public function testStore() {
        $category = factory(Category::class)->create();
        $data = ['name' => 'test'];
        $response = $this->assertStore($data + ['categories_id' => [$category->id]], $data + ['is_active' => true, 'deleted_at' => null]);

        $response->assertJsonStructure([
            'data' => $this->serializedFields + ['categories']
        ]);

        $this->assertRelation($response->json('data.id'), $category->id);

        $id = $response->json('data.id');
        $resource = new GenreResource(Genre::find($id));
        $this->assertResource($response, $resource);

        $data = ['name' => 'test', 'is_active' => false];
        $this->assertStore($data + ['categories_id' => [$category->id]], $data + ['is_active' => false]);
    }

    public function testUpdate() {
        $this->genre = factory(Genre::class)->create([
            'is_active' => false
        ]);

        $category = factory(Category::class)->create();

        $data = ['name' => 'name1', 'is_active' => true];
        $response = $this->assertUpdate($data + ['categories_id' => [$category->id]], $data + ['deleted_at' => null]);

        $id = $response->json('data.id');
        $response->assertJsonStructure([
            'data' => $this->serializedFields + ['categories']
        ]);
        $this->assertRelation($id, $category->id);

        $resource = new GenreResource(Genre::find($id));
        $this->assertResource($response, $resource);
    }

    private function assertHasCategory($genreId, $categoryId) {
        $this->assertDatabaseHas('category_genre', [
            'genre_id' => $genreId,
            'category_id' => $categoryId
        ]);
    }

    public function testRollbackStore() {
        $controller = \Mockery::mock(GenreController::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $controller->shouldReceive('validate')
            ->withAnyArgs()
            ->andReturn(['name' => 'test']);

        $controller->shouldReceive('rulesStore')
            ->withAnyArgs()
            ->andReturn([]);

        $request = \Mockery::mock(Request::class);

        $controller->shouldReceive('handleRelations')
            ->once()
            ->andThrow(new TestExceptions());

        $hasError = false;
        try {
            $controller->store($request);
        } catch (TestExceptions $exp) {
            $this->assertCount(1, Genre::all());
            $hasError = true;
        }

        $this->assertTrue($hasError);
    }

    public function testRollbackUpdate() {
        $controller = \Mockery::mock(GenreController::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $controller->shouldReceive('findOrFail')
            ->withAnyArgs()
            ->andReturn($this->genre);

        $controller->shouldReceive('validate')
            ->withAnyArgs()
            ->andReturn(['name' => 'test']);

        $controller->shouldReceive('rulesUpdate')
            ->withAnyArgs()
            ->andReturn([]);

        $request = \Mockery::mock(Request::class);

        $controller->shouldReceive('handleRelations')
            ->once()
            ->andThrow(new TestExceptions());

        $hasError = false;
        try {
            $controller->update($request, 1);
        } catch (TestExceptions $exp) {
            $this->assertCount(1, Genre::all());
            $hasError = true;
        }

        $this->assertTrue($hasError);
    }

    public function testDestroy() {
        $response = $this->json('DELETE', route('genres.destroy', ['genre' => $this->genre->id]));
        $response->assertStatus(204);
        $this->assertNull(Genre::find($this->genre->id));
        $this->assertNotNull(Genre::withTrashed()->find($this->genre->id));
    }

    /**
     * @return string
     */
    protected function routeStore() {
        return route('genres.store');
    }

    /**
     * @param null $genreId
     * @return string
     */
    protected function routeUpdate($genreId = null) {
        $genreId = $genreId == null ? $this->genre->id : $genreId;
        return route('genres.update', ['genre' => $genreId]);
    }

    /**
     * @return Genre
     */
    protected function model() {
        return get_class($this->genre);
    }

    protected function assertRelation($genreId, $categoryId) {
        $this->assertDatabaseHas('category_genre', [
            'genre_id' => $genreId,
            'category_id' => $categoryId
        ]);
    }

    public function testSyncRelations() {
        $sendData = [
            'name' => 'test'
        ];

        $this->syncRelations($sendData + $this->sendData);
    }

    /**
     * @return array
     */
    protected function relationTables() {
        $relations = [];
        array_push($relations, [
            'table' => 'category_genre',
            'main_key' => 'genre_id',
            'relation_key' => 'category_id',
            'relation_model' => Category::class,
            'main_table_key_relation' => 'categories_id',
            'pivot' => []
        ]);
        return $relations;
    }
}
