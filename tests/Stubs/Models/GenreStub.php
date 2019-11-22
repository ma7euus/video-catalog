<?php

namespace Tests\Stubs\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;

class GenreStub extends Model {

    const TABLE = 'genre_stubs';

    protected $fillable = ['name'];

    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
        $this->table = GenreStub::TABLE;
    }

    public static function createTable() {
        \Schema::create(GenreStub::TABLE, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->timestamps();
        });
    }

    public static function dropTable() {
        \Schema::dropIfExists(GenreStub::TABLE);
    }
}
