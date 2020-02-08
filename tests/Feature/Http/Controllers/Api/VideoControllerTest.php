<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Controllers\Api\VideoController;
use App\Models\Category;
use App\Models\Genre;
use App\Models\Video;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Exceptions\TestExceptions;
use Tests\TestCase;
use Tests\Traits\TestController;
use Tests\Traits\TestRelations;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;
use Illuminate\Http\Request;

class VideoControllerTest extends TestCase {

    use DatabaseMigrations, TestController, TestValidations, TestSaves, TestRelations;

    /**
     * @var Video
     */
    private $video;
    private $sendData;
    private $sendDataRelation;

    protected function setUp(): void {
        parent::setUp();
        $this->video = factory(Video::class)->create([
            'opened' => false
        ]);

        $category = factory(Category::class)->create();
        $genre = factory(Genre::class)->create();
        $genre->categories()->sync($category->id);

        $this->sendData = [
            'title' => 'title',
            'description' => 'description',
            'year_launched' => 2010,
            'duration' => 90,
            'rating' => Video::RATING_LIST[0]
        ];
        $this->sendDataRelation = [
            'categories_id' => [$category->id],
            'genres_id' => [$genre->id]
        ];
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testIndex() {
        $response = $this->get(route('videos.index'));

        $response->assertStatus(200)
            ->assertJson([$this->video->toArray()]);
    }

    public function testShow() {
        $response = $this->get(route('videos.show', ['video' => $this->video->id]));

        $response->assertStatus(200)
            ->assertJson($this->video->toArray());
    }

    public function testInvalidationRequired() {

        $data = [
            'title' => '',
            'description' => '',
            'year_launched' => '',
            'duration' => '',
            'rating' => '',
            'categories_id' => [],
            'genres_id' => [],
        ];
        $this->assertInvalidationInStoreAction($data, 'required');
        $this->assertInvalidationInUpdateAction($data, 'required');
    }

    public function testInvalidationMax() {
        $data = [
            'title' => str_repeat('a', 256),
        ];
        $this->assertInvalidationInStoreAction($data, 'max.string', ['max' => 255]);
        $this->assertInvalidationInUpdateAction($data, 'max.string', ['max' => 255]);
    }

    public function testInvalidationInteger() {
        $data = [
            'duration' => 'a'
        ];
        $this->assertInvalidationInStoreAction($data, 'integer');
        $this->assertInvalidationInUpdateAction($data, 'integer');
    }

    public function testInvalidationBoolean() {
        $data = [
            'opened' => 'a'
        ];
        $this->assertInvalidationInStoreAction($data, 'boolean');
        $this->assertInvalidationInUpdateAction($data, 'boolean');
    }

    public function testInvalidationYearLaunched() {
        $data = [
            'year_launched' => 'a'
        ];
        $this->assertInvalidationInStoreAction($data, 'date_format', ['format' => 'Y']);
        $this->assertInvalidationInUpdateAction($data, 'date_format', ['format' => 'Y']);
    }

    public function testInvalidationRating() {
        $data = [
            'rating' => 0
        ];
        $this->assertInvalidationInStoreAction($data, 'in');
        $this->assertInvalidationInUpdateAction($data, 'in');
    }

    public function testInvalidationArrayExistsFields() {
        $category = factory(Category::class)->create();
        /** @var Genre $genre */
        $genre = factory(Genre::class)->create();
        $genre->categories()->sync([$category->id]);

        $testRules = [];
        array_push($testRules, ['rule' => 'array', 'fields' => [
            'categories_id' => 'a',
            'genres_id' => 'a'
        ]]);

/*        array_push($testRules, ['rule' => 'exists', 'fields' => [
            'categories_id' => [1234],
            'genres_id' => [1234]
        ]]);

        array_push($testRules, ['rule' => 'exists', 'fields' => [
            'categories_id' => [$category->id],
            'genres_id' => [$genre->id]
        ]]);
*/
        foreach ($testRules as $rule) {
            foreach ($rule['fields'] as $field => $value) {
                $this->assertInvalidationInStoreAction([$field => $value], $rule['rule']);
                $this->assertInvalidationInUpdateAction([$field => $value], $rule['rule']);
            }
        }
        $category->delete();
        $genre->delete();
    }

    public function testSave() {

        $data = [
            [
                'send_data' => $this->sendData + $this->sendDataRelation,
                'test_data' => $this->sendData + ['opened' => false]
            ],
            [
                'send_data' => $this->sendData + $this->sendDataRelation + ['opened' => true],
                'test_data' => $this->sendData + ['opened' => true]
            ],
            [
                'send_data' => $this->sendData + $this->sendDataRelation + ['rating' => Video::RATING_LIST[1]],
                'test_data' => $this->sendData + ['rating' => Video::RATING_LIST[1]]
            ]
        ];

        foreach ($data as $key => $value) {
            $response = $this->assertStore($value['send_data'], $value['test_data'] + ['deleted_at' => null]);
            $response->assertJsonStructure([
                'created_at',
                'updated_at',
            ]);
            $this->video->id = $response->json(['id']);
            $response = $this->assertUpdate($value['send_data'], $value['test_data'] + ['deleted_at' => null]);
            $response->assertJsonStructure([
                'created_at',
                'updated_at',
            ]);
            $this->assertHasCategory($response->json('id'), current($value['send_data']['categories_id']));
            $this->assertHasGenre($response->json('id'), current($value['send_data']['genres_id']));
        }
    }

    private function assertHasCategory($videoId, $categoryId) {
        $this->assertDatabaseHas('category_video', [
            'video_id' => $videoId,
            'category_id' => $categoryId
        ]);
    }

    private function assertHasGenre($videoId, $genreId) {
        $this->assertDatabaseHas('genre_video', [
            'video_id' => $videoId,
            'genre_id' => $genreId
        ]);
    }

//    /*public function testRollbackStore() {
//        $controller = \Mockery::mock(VideoController::class)
//            ->makePartial()
//            ->shouldAllowMockingProtectedMethods();
//
//        $controller->shouldReceive('validate')
//            ->withAnyArgs()
//            ->andReturn($this->sendData + $this->sendDataRelation);
//
//        $controller->shouldReceive('rulesStore')
//            ->withAnyArgs()
//            ->andReturn([]);
//
//        $request = \Mockery::mock(Request::class);
//
//        $controller->shouldReceive('handleRelations')
//            ->once()
//            ->andThrow(new TestExceptions());
//
//        try {
//            $controller->store($request);
//        } catch (TestExceptions $exp) {
//            $this->assertCount(1, Video::all());
//        }
//    }
//
//    public function testRollbackUpdate() {
//        $controller = \Mockery::mock(VideoController::class)
//            ->makePartial()
//            ->shouldAllowMockingProtectedMethods();
//
//        $controller->shouldReceive('findOrFail')
//            ->withAnyArgs()
//            ->andReturn($this->video);
//
//        $controller->shouldReceive('validate')
//            ->withAnyArgs()
//            ->andReturn(['name' => 'test']);
//
//        $controller->shouldReceive('rulesUpdate')
//            ->withAnyArgs()
//            ->andReturn([]);
//
//        $request = \Mockery::mock(Request::class);
//
//        $controller->shouldReceive('handleRelations')
//            ->once()
//            ->andThrow(new TestExceptions());
//
//        $hasError = false;
//        try {
//            $controller->update($request, 1);
//        } catch (TestExceptions $exp) {
//            $this->assertCount(1, Video::all());
//            $hasError = true;
//        }
//
//        $this->assertTrue($hasError);
//    }*/

    public function testDestroy() {
        $response = $this->json('DELETE', route('videos.destroy', ['video' => $this->video->id]));
        $response->assertStatus(204);
        $this->assertNull(Video::find($this->video->id));
        $this->assertNotNull(Video::withTrashed()->find($this->video->id));
    }

    /**
     * @return string
     */
    protected function routeStore() {
        return route('videos.store');
    }

    /**
     * @return string
     */
    protected function routeUpdate() {
        return route('videos.update', ['video' => $this->video->id]);
    }

    /**
     * @return Video
     */
    protected function model() {
        return get_class($this->video);
    }

    public function testSyncRelations() {
        $this->syncRelations($this->sendData + $this->sendDataRelation);
    }

    /**
     * @return array
     */
    protected function relationTables() {
        $relations = [];
        array_push($relations, [
            'table' => 'category_video',
            'main_key' => 'video_id',
            'relation_key' => 'category_id',
            'relation_model' => Category::class,
            'main_table_key_relation' => 'categories_id',
            'pivot' => [
                'genres' => 'genres_id'
            ]
        ]);
        array_push($relations, [
            'table' => 'genre_video',
            'main_key' => 'video_id',
            'relation_key' => 'genre_id',
            'relation_model' => Genre::class,
            'main_table_key_relation' => 'genres_id',
            'pivot' => [
                'categories' => 'categories_id'
            ]
        ]);
        return $relations;
    }

}
