<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Collection;

class GenresHasCategoriesRule implements Rule {

    /**
     * @var array
     */
    private $categoriesId;

    /**
     * @var array
     */
    private $genresId;

    /**
     * GenresHasCategoriesRule constructor.
     * @param array $categoriesId
     */
    public function __construct(array $categoriesId) {
        $this->categoriesId = array_unique($categoriesId);
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value) {

        $value = is_array($value) ? $value : [];
        $this->genresId = array_unique($value);

        if (!count($this->genresId) || !count($this->categoriesId)) {
            return false;
        }

        $categoriesFound = [];
        foreach ($this->genresId as $genreId) {
            $rows = $this->getRows($genreId);
            if (!$rows->count()) {
                return false;
            }
            array_push($categoriesFound, ...$rows->pluck('category_id')->toArray());
        }

        if (count(array_unique($categoriesFound)) !== count($this->categoriesId)) {
            return false;
        }
        return true;
    }

    /**
     * @param $genreId
     * @return Collection
     */
    protected function getRows($genreId): Collection {
        return \DB::table('category_genre')
            ->where('genre_id', $genreId)
            ->whereIn('category_id', $this->categoriesId)
            ->get();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message() {
        return 'A genre ID must be related at least a category ID';
    }
}
