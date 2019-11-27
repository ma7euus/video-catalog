<?php

namespace Tests\Unit\Models;

use App\Models\Traits\Uuid;
use App\Models\Video;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tests\TestCase;

class VideoUnitTest extends TestCase {

    /**
     * @var Video
     */
    private $video;

    protected function setUp(): void {
        parent::setUp();
        $this->video = new Video();
    }

    public function testIfUseTraits() {
        $traits = [
            SoftDeletes::class, Uuid::class
        ];
        $videoTraits = array_keys(class_uses(get_class($this->video)));
        $this->assertEquals($traits, $videoTraits);
    }

    public function testFillableAttribute() {
        $fillable = [
            'title',
            'description',
            'year_launched',
            'opened',
            'duration',
            'rating'
        ];
        $this->assertEquals($fillable, $this->video->getFillable());
    }

    public function testCastsAttribute() {
        $casts = [
            'id' => 'string',
            'opened' => 'boolean',
            'year_launched' => 'integer',
            'duration' => 'integer',
        ];
        $this->assertEquals($casts, $this->video->getCasts());
    }

    public function testDatesAttribute() {
        $dates = ['deleted_at', 'created_at', 'updated_at'];
        $videoDates = $this->video->getDates();
        $this->assertEqualsCanonicalizing($dates, $videoDates);
        $this->assertCount(count($dates), $videoDates);
    }

    public function testIncrementingAttribute() {
        $this->assertEquals(false, $this->video->getIncrementing());
    }
}
