<?php

namespace App\Models;

use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Video extends Model {

    use SoftDeletes, Uuid;

    const RATING_LIST = ['L', '10', '12', '14', '18'];

    protected $fillable = [
        'title',
        'description',
        'year_launched',
        'opened',
        'duration',
        'rating'
    ];

    protected $dates = ['deleted_at'];

    protected $casts = [
        'id' => 'string',
        'opened' => 'boolean',
        'year_launched' => 'integer',
        'duration' => 'integer',
    ];

    public $incrementing = false;


    /**
     * @param array $attributes
     * @return Video|\Illuminate\Database\Eloquent\Builder|Model
     */
    public static function create(array $attributes = []) {

        try {
            \DB::beginTransaction();
            $obj = static::query()->create($attributes);
            static::handleRelations($obj, $attributes);
            \DB::commit();
            return $obj;
        } catch (\Exception $e) {
            if (isset($obj)) {

            }
            \DB::rollBack();
            throw $e;
        }
    }

    public function update(array $attributes = [], array $options = []) {
        try {
            \DB::beginTransaction();
            $saved = parent::update($attributes, $options);
            if ($saved) {
                static::handleRelations($this, $attributes);
            }
            \DB::commit();
            return $saved;
        } catch (\Exception $e) {
            \DB::rollBack();
            throw $e;
        }
    }

    /**
     * @param Video $video
     * @param array $attributes
     * @return Video
     */
    protected static function handleRelations(Video $video, array $attributes) {
        if (isset($attributes['categories_id'])) {
            $video->categories()->sync($attributes['categories_id']);
        }
        if (isset($attributes['genres_id'])) {
            $video->genres()->sync($attributes['genres_id']);
        }
        return $video;
    }

    public function categories() {
        return $this->belongsToMany(Category::class)->withTrashed();
    }

    public function genres() {
        return $this->belongsToMany(Genre::class)->withTrashed();
    }
}
