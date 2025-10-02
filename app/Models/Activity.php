<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'parent_id',
        'level',
    ];

    protected $casts = [
        'level' => 'integer',
    ];

    /**
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        static::saving(function ($activity) {
            if ($activity->parent_id) {
                $parent = Activity::find($activity->parent_id);
                if (!$parent) {
                    throw new \Exception('Родительская деятельность не найдена');
                }
                if ($parent->level >= 3) {
                    throw new \Exception('Максимальная вложенность — 3 уровня');
                }
                $activity->level = $parent->level + 1;
            } else {
                $activity->level = 1;
            }
        });
    }

    /**
     * @return BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Activity::class, 'parent_id');
    }

    /**
     * @return HasMany
     */
    public function children(): HasMany
    {
        return $this->hasMany(Activity::class, 'parent_id');
    }

    /**
     * @return BelongsToMany
     */
    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class, 'organization_activities')
            ->withTimestamps();
    }

    /**
     * @return HasMany
     */
    public function descendants(): HasMany
    {
        return $this->children()->with('descendants');
    }

    /**
     * Получить все ID потомков для поиска организаций
     *
     * @param int $activityId
     * @return array
     */
    public static function getDescendantIds(int $activityId): array
    {
        $descendants = [];
        $toProcess = [$activityId];

        while (!empty($toProcess)) {
            $currentIds = $toProcess;
            $toProcess = [];

            $children = self::whereIn('parent_id', $currentIds)->pluck('id')->toArray();
            $descendants = array_merge($descendants, $children);
            $toProcess = $children;
        }

        return array_unique(array_merge([$activityId], $descendants));
    }
}
