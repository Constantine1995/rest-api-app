<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Activity;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ActivityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Проверяет, что getDescendantIds возвращает ID активности и всех её потомков.
     */
    public function test_get_descendant_ids_returns_activity_and_all_descendants()
    {
        $level1 = Activity::create(['name' => 'Level 1', 'level' => 1, 'parent_id' => null]);
        $level2a = Activity::create(['name' => 'Level 2A', 'level' => 2, 'parent_id' => $level1->id]);
        $level2b = Activity::create(['name' => 'Level 2B', 'level' => 2, 'parent_id' => $level1->id]);
        $level3 = Activity::create(['name' => 'Level 3', 'level' => 3, 'parent_id' => $level2a->id]);

        $descendantIds = Activity::getDescendantIds($level1->id);

        $this->assertEqualsCanonicalizing(
            [$level1->id, $level2a->id, $level2b->id, $level3->id],
            $descendantIds,
        );

        $this->assertCount(4, $descendantIds, 'Дубликаты в потомках недопустимы');
    }

    /**
     * Проверяет, что getDescendantIds возвращает только ID активности, если потомков нет.
     */
    public function test_get_descendant_ids_for_leaf_activity()
    {
        $leaf = Activity::create(['name' => 'Leaf Activity', 'level' => 1, 'parent_id' => null]);

        $descendantIds = Activity::getDescendantIds($leaf->id);

        $this->assertEquals(
            [$leaf->id],
            $descendantIds,
        );

        $this->assertCount(1, $descendantIds, 'Для листовой активности должен возвращаться только её ID');
    }

    /**
     * Проверяет, что getDescendantIds корректно обрабатывает несуществующий ID.
     */
    public function test_get_descendant_ids_for_non_existent_activity()
    {
        $descendantIds = Activity::getDescendantIds(999);

        $this->assertEquals(
            [999],
            $descendantIds,
        );

        $this->assertCount(1, $descendantIds, 'Для несуществующего ID должен возвращаться только он сам');
    }

    /**
     * Проверяет, что getDescendantIds корректно обрабатывает вложенную иерархию.
     */
    public function test_get_descendant_ids_with_nested_hierarchy()
    {
        $level1 = Activity::create(['name' => 'Level 1', 'level' => 1, 'parent_id' => null]);
        $level2a = Activity::create(['name' => 'Level 2A', 'level' => 2, 'parent_id' => $level1->id]);
        $level3a = Activity::create(['name' => 'Level 3A', 'level' => 3, 'parent_id' => $level2a->id]);
        $level3b = Activity::create(['name' => 'Level 3B', 'level' => 3, 'parent_id' => $level2a->id]);
        $level2b = Activity::create(['name' => 'Level 2B', 'level' => 2, 'parent_id' => $level1->id]);

        $descendantIds = Activity::getDescendantIds($level1->id);

        $this->assertEqualsCanonicalizing(
            [$level1->id, $level2a->id, $level2b->id, $level3a->id, $level3b->id],
            $descendantIds,
        );

        $this->assertCount(5, $descendantIds, 'Дубликаты в потомках недопустимы');

        $descendantIdsLevel2a = Activity::getDescendantIds($level2a->id);
        $this->assertEqualsCanonicalizing(
            [$level2a->id, $level3a->id, $level3b->id],
            $descendantIdsLevel2a,
        );
    }
}
