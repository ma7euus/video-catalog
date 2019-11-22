<?php

namespace Tests\Unit\Models;

use App\Models\Genre;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tests\TestCase;

class GenreTest extends TestCase {
    /**
     * @var Genre
     */
    private $genre;

    protected function setUp(): void {
        parent::setUp();
        $this->genre = new Genre();
    }

    public function testIfUseTraits() {
        $traits = [
            SoftDeletes::class, Uuid::class
        ];
        $categoryTraits = array_keys(class_uses(get_class($this->genre)));
        $this->assertEquals($traits, $categoryTraits);
    }

    public function testFillableAttribute() {
        $fillable = ['name', 'is_active'];
        $this->assertEquals($fillable, $this->genre->getFillable());
    }

    public function testCastsAttribute() {
        $casts = ['id' => 'string', 'is_active' => 'boolean'];
        $this->assertEquals($casts, $this->genre->getCasts());
    }

    public function testDatesAttribute() {
        $dates = ['deleted_at', 'created_at', 'updated_at'];
        $genreDates = $this->genre->getDates();
        $this->assertEqualsCanonicalizing($dates, $genreDates);
        $this->assertCount(count($dates), $genreDates);
    }

    public function testIncrementingAttribute() {
        $this->assertEquals(false, $this->genre->getIncrementing());
    }
}
