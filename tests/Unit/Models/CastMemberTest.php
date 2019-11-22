<?php

namespace Tests\Unit\Models;

use App\Models\CastMember;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tests\TestCase;

class CastMemberTest extends TestCase {
    /**
     * @var CastMember
     */
    private $castMember;

    protected function setUp(): void {
        parent::setUp();
        $this->castMember = new CastMember();
    }

    public function testIfUseTraits() {
        $traits = [
            SoftDeletes::class, Uuid::class
        ];
        $categoryTraits = array_keys(class_uses(get_class($this->castMember)));
        $this->assertEquals($traits, $categoryTraits);
    }

    public function testFillableAttribute() {
        $fillable = ['name', 'type'];
        $this->assertEquals($fillable, $this->castMember->getFillable());
    }

    public function testCastsAttribute() {
        $casts = ['id' => 'string', 'type' => 'integer'];
        $this->assertEquals($casts, $this->castMember->getCasts());
    }

    public function testDatesAttribute() {
        $dates = ['deleted_at', 'created_at', 'updated_at'];
        $genreDates = $this->castMember->getDates();
        $this->assertEqualsCanonicalizing($dates, $genreDates);
        $this->assertCount(count($dates), $genreDates);
    }

    public function testIncrementingAttribute() {
        $this->assertEquals(false, $this->castMember->getIncrementing());
    }
}
