<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $primaryKey = "uuid";

    public $incrementing = false;

    protected $fillable = ["uuid", "type", "content", "sort_order", "date_created", "last_update", "done"];

    public $timestamps = false;

    public static function reorderTask($oldSortOrder, $newSortOrder)
    {
        $orderCriteria = "DESC";
        if ($newSortOrder > $oldSortOrder) {
            $orderCriteria = "ASC";
        }

        $tasks = Task::orderBy("sort_order", "ASC")
            ->orderBy('last_update', $orderCriteria)
            ->get();

        for ($index = 0; $index < count($tasks); $index++) {
            $task = $tasks[$index];
            $task->sort_order = $index + 1;
            $task->save();
        }
    }
}
