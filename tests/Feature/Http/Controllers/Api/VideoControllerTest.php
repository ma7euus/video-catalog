<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Controllers\Api\VideoController;
use App\Models\Category;
use App\Models\Genre;
use App\Models\Video;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Tests\Exceptions\TestExceptions;
use Tests\TestCase;
use Tests\Traits\TestController;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;
use Illuminate\Http\Request;

class VideoControllerTest extends TestCase {

    use DatabaseMigrations, TestController, TestValidations, TestSaves;

    /**
     * @var Video
     */
    private $video;
    private $sendData;

    protected function setUp(): void {
        parent::setUp();
        $this->video = factory(Video::class)->create([
            'opened' => false
        ]);

        $category = factory(Category::class)->create();
        $genre = factory(Genre::class)->create();

        $this->sendData = [
            'title' => 'title',
            'description' => 'description',
            'year_launched' => 2010,
            'duration' => 90,
            'rating' => Video::RATING_LIST[0],
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
        $testRules = [
            'array' => [
                'categories_id' => 'a',
                'genres_id' => 'a'
            ],
            'exists' => [
                'categories_id' => [1234],
                'genres_id' => [1234]
            ]
        ];

        foreach ($testRules as $rule => $data) {
            foreach ($data as $field => $value) {
                $this->assertInvalidationInStoreAction([$field => $value], $rule);
                $this->assertInvalidationInUpdateAction([$field => $value], $rule);
            }
        }
    }

    public function testSave() {

        $data = [
            [
                'send_data' => $this->sendData,
                'test_data' => $this->sendData + ['opened' => false]
            ],
            [
                'send_data' => $this->sendData + ['opened' => true],
                'test_data' => $this->sendData + ['opened' => true]
            ],
            [
                'send_data' => $this->sendData + ['rating' => Video::RATING_LIST[1]],
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
        }
    }

    public function testRollbackStore() {
        $controller = \Mockery::mock(VideoController::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $controller->shouldReceive('validate')
            ->withAnyArgs()
            ->andReturn($this->sendData);

        $controller->shouldReceive('rulesStore')
            ->withAnyArgs()
            ->andReturn([]);

        $request = \Mockery::mock(Request::class);

        $controller->shouldReceive('handleRelations')
            ->once()
            ->andThrow(new TestExceptions());

        try {
            $controller->store($request);
        } catch (TestExceptions $exp) {
            $this->assertCount(1, Video::all());
        }
    }

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
}
