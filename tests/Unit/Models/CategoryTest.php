<?php

namespace Tests\Unit\Models;

use App\Models\CategoryStub;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tests\TestCase;

class CategoryTest extends TestCase {

    /**
     * @var CategoryStub
     */
    private $category;

    protected function setUp(): void {
        parent::setUp();
        $this->category = new CategoryStub();
    }

    public function testIfUseTraits() {
        $traits = [
            SoftDeletes::class, Uuid::class
        ];
        $categoryTraits = array_keys(class_uses(get_class($this->category)));
        $this->assertEquals($traits, $categoryTraits);
    }

    public function testFillableAttribute() {
        $fillable = ['name', 'description', 'is_active'];
        $this->assertEquals($fillable, $this->category->getFillable());
    }

    public function testCastsAttribute() {
        $casts = ['id' => 'string', 'is_active' => 'boolean'];
        $this->assertEquals($casts, $this->category->getCasts());
    }

    public function testDatesAttribute() {
        $dates = ['deleted_at', 'created_at', 'updated_at'];
        $categoryDates = $this->category->getDates();
        $this->assertEqualsCanonicalizing($dates, $categoryDates);
        $this->assertCount(count($dates), $categoryDates);
    }

    public function testIncrementingAttribute() {
        $this->assertEquals(false, $this->category->getIncrementing());
    }
}
