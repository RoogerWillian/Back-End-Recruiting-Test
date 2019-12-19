<?php

namespace App\Observers;

use App\Task;

class TaskObserver
{
    /**
     * Handle the task "created" event.
     *
     * @param \App\Task $task
     * @return void
     */
    public function created(Task $task)
    {

    }

    public function updating(Task $task)
    {
        $task->last_update = date("Y-m-d H:i:s");
    }

    /**
     * Handle the task "updated" event.
     *
     * @param \App\Task $task
     * @return void
     */
    public function updated(Task $task)
    {

    }

    /**
     * Handle the task "deleted" event.
     *
     * @param \App\Task $task
     * @return void
     */
    public function deleted(Task $task)
    {
        //
    }

    /**
     * Handle the task "restored" event.
     *
     * @param \App\Task $task
     * @return void
     */
    public function restored(Task $task)
    {
        //
    }

    /**
     * Handle the task "force deleted" event.
     *
     * @param \App\Task $task
     * @return void
     */
    public function forceDeleted(Task $task)
    {
        //
    }
}
