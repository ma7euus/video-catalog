<?php

namespace Tests\Feature\Http\Controllers\Api\VideoController;

use App\Models\Video;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

abstract class BaseVideoControllerTestCase extends TestCase {
    use DatabaseMigrations;

    protected $video;
    protected $sendData;
    protected $sendDataRelation;

    protected function setUp(): void {
        parent::setUp();
        $this->video = factory(Video::class)->create([
            'opened' => false
        ]);
        $this->sendData = [
            'title' => 'title',
            'description' => 'description',
            'year_launched' => 2010,
            'rating' => Video::RATING_LIST[0],
            'duration' => 90
        ];

        $category = factory(Category::class)->create();
        $genre = factory(Genre::class)->create();
        $genre->categories()->sync($category->id);

        $this->sendDataRelation = [
            'categories_id' => [$category->id],
            'genres_id' => [$genre->id]
        ];
    }
}
