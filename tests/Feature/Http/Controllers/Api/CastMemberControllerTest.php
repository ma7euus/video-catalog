<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Resources\CastMemberResource;
use App\Models\CastMember;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\TestController;
use Tests\Traits\TestResources;
use Tests\Traits\TestValidations;

class CastMemberControllerTest extends TestCase {

    use DatabaseMigrations, TestController, TestValidations, TestResources;

    /**
     * @var CastMember
     */
    private $castMember;
    private $serializedFields = [
        'id',
        'name',
        'type',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected function setUp(): void {
        parent::setUp();
        $this->castMember = factory(CastMember::class)->create();
    }

    public function testIndex() {
        $response = $this->get(route('cast_members.index'));

        $response
            ->assertStatus(200)
            ->assertJson([
                'meta' => ['per_page' => 15]
            ])
            ->assertJsonStructure([
                'data' => [
                    '*' => $this->serializedFields
                ],
                'links' => [],
                'meta' => []
            ]);

        $resource = CastMemberResource::collection(collect([$this->castMember]));
        $this->assertResource($response, $resource);
    }

    public function testShow() {
        $response = $this->get(route('cast_members.show', ['cast_member' => $this->castMember->id]));

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => $this->serializedFields
            ]);

        $id = $response->json('data.id');
        $resource = new CastMemberResource(CastMember::find($id));
        $this->assertResource($response, $resource);
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

        $id = $response->json('data.id');
        $castMember = new CastMemberResource(CastMember::find($id));

        $response->assertStatus(201)
            ->assertJson($castMember->response()->getData(true));

        $this->assertEquals($response->json('data.type'), CastMember::TYPE_ACTOR);

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

        $id = $response->json('data.id');
        $castMember = new CastMemberResource(CastMember::find($id));

        $response->assertStatus(200)
            ->assertJson($castMember->response()->getData(true))
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
