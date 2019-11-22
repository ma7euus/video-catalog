<?php

namespace Tests\Stubs\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;

class CategoryStub extends Model {

    const TABLE = 'category_stubs';

    protected $fillable = ['name', 'description'];

    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
        $this->table = CategoryStub::TABLE;
    }

    public static function createTable() {
        \Schema::create(CategoryStub::TABLE, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public static function dropTable() {
        \Schema::dropIfExists(CategoryStub::TABLE);
    }
}
