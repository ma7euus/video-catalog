<?php

namespace Tests\Feature\Models;

use App\Models\CastMember;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class CastMemberTest extends TestCase {

    use DatabaseMigrations;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testList() {
        factory(CastMember::class, 1)->create();
        $castMembers = CastMember::all();
        $this->assertCount(1, $castMembers);
        $castMemberKey = array_keys($castMembers->first()->getAttributes());
        $this->assertEqualsCanonicalizing([
            "id",
            "name",
            "type",
            "created_at",
            "updated_at",
            "deleted_at"
        ],
            $castMemberKey);
    }

    public function testCreate() {
        $castMember = CastMember::create([
            'name' => 'test1'
        ]);
        $castMember->refresh();

        $this->assertEquals(36, strlen($castMember->id));
        $this->assertEquals('test1', $castMember->name);
        $this->assertEquals($castMember->type, CastMember::TYPE_ACTOR);

        $castMember = CastMember::create([
            'name' => 'test1',
            'type' => CastMember::TYPE_ACTOR
        ]);
        $this->assertEquals($castMember->type, CastMember::TYPE_ACTOR);

        $castMember = CastMember::create([
            'name' => 'test1',
            'type' => CastMember::TYPE_DIRECTOR
        ]);
        $this->assertEquals($castMember->type, CastMember::TYPE_DIRECTOR);
    }

    public function testUpdate() {
        /** @var CastMember $castMember */
        $castMember = factory(CastMember::class)->create([
            'name' => 'test_name',
            'type' => CastMember::TYPE_ACTOR
        ])->first();

        $data = [
            'name' => 'test_name_updated',
            'type' => CastMember::TYPE_DIRECTOR
        ];
        $castMember->update($data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $castMember->{$key});
        }
    }

    public function testDelete() {
        /**@var CastMember $castMember */
        $castMember = factory(CastMember::class)->create();
        $castMember->delete();
        $this->assertNull(CastMember::find($castMember->id));

        $castMember->restore();
        $this->assertNotNull(CastMember::find($castMember->id));
    }
}
