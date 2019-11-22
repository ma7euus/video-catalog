<?php

namespace Tests\Feature\Models;

use App\Models\GenreStub;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class GenreTest extends TestCase {

    use DatabaseMigrations;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testList() {
        factory(GenreStub::class, 1)->create();
        $genres = GenreStub::all();
        $this->assertCount(1, $genres);
        $genreKey = array_keys($genres->first()->getAttributes());
        $this->assertEqualsCanonicalizing([
            "id",
            "name",
            "is_active",
            "created_at",
            "updated_at",
            "deleted_at"
        ],
            $genreKey);
    }

    public function testCreate() {
        $genre = GenreStub::create([
            'name' => 'test1'
        ]);
        $genre->refresh();

        $this->assertEquals(36, strlen($genre->id));
        $this->assertEquals('test1', $genre->name);
        $this->assertTrue($genre->is_active);

        $genre = GenreStub::create([
            'name' => 'test1',
            'is_active' => false
        ]);
        $this->assertFalse($genre->is_active);

        $genre = GenreStub::create([
            'name' => 'test1',
            'is_active' => true
        ]);
        $this->assertTrue($genre->is_active);
    }

    public function testUpdate() {
        /** @var GenreStub $genre */
        $genre = factory(GenreStub::class)->create([
            'name' => 'test_name',
            'is_active' => false
        ])->first();

        $data = [
            'name' => 'test_name_updated',
            'is_active' => true,
        ];
        $genre->update($data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $genre->{$key});
        }
    }

    public function testDelete() {
        /**@var GenreStub $genre */
        $genre = factory(GenreStub::class)->create();
        $genre->delete();
        $this->assertNull(GenreStub::find($genre->id));

        $genre->restore();
        $this->assertNotNull(GenreStub::find($genre->id));
    }
}
