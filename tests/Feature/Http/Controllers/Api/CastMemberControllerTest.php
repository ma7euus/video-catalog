<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\CastMember;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\TestController;
use Tests\Traits\TestValidations;

class CastMemberControllerTest extends TestCase {

    use DatabaseMigrations, TestController, TestValidations;

    /**
     * @var CastMember
     */
    private $castMember;

    protected function setUp(): void {
        parent::setUp();
        $this->castMember = factory(CastMember::class)->create();
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testIndex() {
        $response = $this->get(route('cast_members.index'));

        $response->assertStatus(200)
            ->assertJson([$this->castMember->toArray()]);
    }

    public function testShow() {
        $response = $this->get(route('cast_members.show', ['cast_member' => $this->castMember->id]));

        $response->assertStatus(200)
            ->assertJson($this->castMember->toArray());
    }

    public function testInvalidationData() {

        $data = [
            'name' => ''
        ];
        $this->assertInvalidationInStoreAction($data, 'required');
        $this->assertInvalidationInUpdateAction($data, 'required');

        $data = [
            'name' => str_repeat('a', 256),
        ];
        $this->assertInvalidationInStoreAction($data, 'max.string', ['max' => 255]);
        $this->assertInvalidationInUpdateAction($data, 'max.string', ['max' => 255]);

        $data = [
            'type' => 'a'
        ];
        $this->assertInvalidationInStoreAction($data, 'in');
        $this->assertInvalidationInUpdateAction($data, 'in');
    }

    public function testStore() {
        $response = $this->json('POST', route('cast_members.store'), [
            'name' => 'test',
            'type' => CastMember::TYPE_ACTOR
        ]);

        $id = $response->json('id');
        $castMember = CastMember::find($id);

        $response->assertStatus(201)
            ->assertJson($castMember->toArray());

        $this->assertEquals($response->json('type'), CastMember::TYPE_ACTOR);

        $response = $this->json(
            'POST',
            route('cast_members.store'),
            [
                'name' => 'test',
                'type' => CastMember::TYPE_DIRECTOR
            ]
        );

        $response->assertJsonFragment([
            'type' => CastMember::TYPE_DIRECTOR
        ]);

    }

    public function testUpdate() {

        $castMember = factory(CastMember::class)->create([
            'type' => CastMember::TYPE_DIRECTOR
        ]);
        $response = $this->json('PUT', route('cast_members.update', ['cast_member' => $castMember->id]), [
            'name' => 'test',
            'type' => CastMember::TYPE_ACTOR
        ]);

        $id = $response->json('id');
        $castMember = CastMember::find($id);

        $response->assertStatus(200)
            ->assertJson($castMember->toArray())
            ->assertJsonFragment([
                'type' => CastMember::TYPE_ACTOR
            ]);
    }

    public function testDestroy() {
        $response = $this->json('DELETE', route('cast_members.destroy', ['cast_member' => $this->castMember->id]));
        $response->assertStatus(204);
        $this->assertNull(CastMember::find($this->castMember->id));
        $this->assertNotNull(CastMember::withTrashed()->find($this->castMember->id));
    }

    /**
     * @return string
     */
    protected function routeStore() {
        return route('cast_members.store');
    }

    /**
     * @return string
     */
    protected function routeUpdate() {
        return route('cast_members.update', ['cast_member' => $this->castMember->id]);
    }

    /**
     * @return CastMember
     */
    protected function model() {
        return get_class($this->castMember);
    }

}
