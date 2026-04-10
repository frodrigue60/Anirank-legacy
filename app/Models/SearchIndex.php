<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Represents a row in the centralized `search_index` table.
 *
 * The `search_vector` column is a GENERATED ALWAYS column managed by PostgreSQL;
 * it must never be written to from PHP.
 *
 * @property int    $id
 * @property string $item_type   e.g. 'anime', 'song', 'artist', 'user', 'studio', 'producer'
 * @property string $item_id     UUID of the original record
 * @property string $title
 * @property string|null $subtitle
 * @property string $slug
 * @property string|null $image_url
 */
class SearchIndex extends Model
{
    protected $table = 'search_index';

    /**
     * The generated column must not be mass-assigned or written to.
     */
    protected $guarded = ['id', 'search_vector'];

    // --------------------------------------------------------- //
    // Scopes
    // --------------------------------------------------------- //

    /**
     * Full-text search using the pre-built tsvector column.
     *
     * The `simple` dictionary is locale-agnostic and works with Japanese /
     * romanized anime titles out of the box.
     *
     * Usage:
     *   SearchIndex::search('naruto')->get();
     *   SearchIndex::search('yoasobi')->forTypes(['artist'])->get();
     *
     * @param Builder $query
     * @param string  $term  Raw search term supplied by the user
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        // Sanitize: strip characters that break tsquery (quotes, colons, etc.)
        $term = trim(preg_replace('/[^a-zA-Z0-9\s\-_\'\.]/u', ' ', $term));

        if (empty($term)) {
            return $query->whereRaw('FALSE'); // Return nothing for empty queries
        }

        // Build a prefix query: "narut" matches "naruto", "naruto shippuden", etc.
        $tsQuery = collect(preg_split('/\s+/', $term))
            ->filter()
            ->map(fn (string $word) => $word . ':*')   // prefix matching
            ->implode(' & ');

        return $query
            ->whereRaw("search_vector @@ to_tsquery('simple', ?)", [$tsQuery])
            ->orderByRaw("ts_rank(search_vector, to_tsquery('simple', ?)) DESC", [$tsQuery]);
    }

    /**
     * Restrict results to specific entity types.
     *
     * @param Builder  $query
     * @param string[] $types  e.g. ['anime', 'song']
     */
    public function scopeForTypes(Builder $query, array $types): Builder
    {
        return $query->whereIn('item_type', $types);
    }

    /**
     * Convenience: search limited to a single type.
     *
     * @param Builder $query
     * @param string  $type   e.g. 'anime'
     */
    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('item_type', $type);
    }
}
