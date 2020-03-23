<?php

namespace App\Models;

use App\ModelFilters\CastMemberFilter;
use App\Models\Traits\Uuid;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CastMember extends Model {

    const TYPE_DIRECTOR = 1;
    const TYPE_ACTOR = 2;

    use SoftDeletes, Uuid, Filterable;

    protected $fillable = ['name', 'type'];

    protected $dates = ['deleted_at'];

    protected $casts = ['id' => 'string', 'type' => 'integer'];

    public $incrementing = false;


    public function modelFilter() {
        return $this->provideFilter(CastMemberFilter::class);
    }
}
