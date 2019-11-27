<?php

namespace Tests\Feature\Models;

use App\Models\Video;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class VideoTest extends TestCase {

    use DatabaseMigrations;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testList() {
        factory(Video::class, 1)->create();
        $videos = Video::all();
        $this->assertCount(1, $videos);
        $videoKey = array_keys($videos->first()->getAttributes());
        $this->assertEqualsCanonicalizing([
            "id",
            'title',
            'description',
            'year_launched',
            'opened',
            'duration',
            'rating',
            "created_at",
            "updated_at",
            "deleted_at"
        ],
            $videoKey);
    }

    public function testCreate() {

        $video = Video::create([
            'title' => 'test1',
            'description' => 'test_description',
            'year_launched' => 2018,
            'duration' => 29,
            'rating' => current(Video::RATING_LIST)
        ]);
        $video->refresh();
        $this->assertFalse($video->opened);

        $video = Video::create([
            'title' => 'test1',
            'description' => 'test_description',
            'year_launched' => 2018,
            'duration' => 29,
            'opened' => true,
            'rating' => current(Video::RATING_LIST)
        ]);
        $this->assertTrue($video->opened);
    }

    public function testUpdate() {
        /** @var Video $video */
        $video = factory(Video::class)->create([
            'description' => 'test_description',
            'year_launched' => 2020,
            'opened' => true,
            'duration' => 29,
            'rating' => current(Video::RATING_LIST)
        ])->first();

        $data = [
            'title' => 'test_title',
            'description' => 'test_description_2',
            'year_launched' => 2019,
            'opened' => false,
            'duration' => 25,
            'rating' => last(Video::RATING_LIST)
        ];
        $video->update($data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $video->{$key});
        }
    }

    public function testDelete() {
        /**@var Video $video */
        $video = factory(Video::class)->create();
        $video->delete();
        $this->assertNull(Video::find($video->id));

        $video->restore();
        $this->assertNotNull(Video::find($video->id));
    }
}
