<?php

namespace App\Models;

use App\Models\Traits\UploadFiles;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Video extends Model {

    use SoftDeletes, Uuid, UploadFiles;

    const RATING_LIST = ['L', '10', '12', '14', '18'];

    const MAX_VIDEO_SIZE = 1024 * 1024 * 50;
    const MAX_THUMB_SIZE = 1024 * 5;
    const MAX_BANNER_SIZE = 1024 * 10;
    const MAX_TRAILER_SIZE = 1024 * 1024 * 1;

    protected $fillable = [
        'title',
        'description',
        'year_launched',
        'opened',
        'rating',
        'duration',
        'video_file',
        'thumb_file',
        'trailer_file',
        'banner_file'
    ];

    protected $dates = ['deleted_at'];

    protected $casts = [
        'id' => 'string',
        'opened' => 'boolean',
        'year_launched' => 'integer',
        'duration' => 'integer',
    ];

    public $incrementing = false;

    public static $fileFields = ['video_file', 'thumb_file', 'banner_file', 'trailer_file'];

    /**
     * @param array $attributes
     * @return Video
     * @throws \Exception
     */
    public static function create(array $attributes = []) {

        $files = self::extractFiles($attributes);
        try {
            \DB::beginTransaction();
            /** @var Video $obj */
            $obj = static::query()->create($attributes);
            static::handleRelations($obj, $attributes);
            $obj->uploadFiles($files);
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
        $files = self::extractFiles($attributes);
        try {
            \DB::beginTransaction();
            $saved = parent::update($attributes, $options);
            if ($saved) {
                static::handleRelations($this, $attributes);
                $this->uploadFiles($files);
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

    protected function uploadDir() {
        return $this->id;
    }
}
