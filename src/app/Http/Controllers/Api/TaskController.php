<?php

namespace App\Http\Controllers\Api;

use App\Filters\TaskFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\TaskRequest;
use App\Models\Task;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(TaskFilter $request)
    {
        $task = Task::filter($request)->get()->toTree();

        return response()->json($task);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\TaskRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TaskRequest $request)
    {
        $task = Task::create([
        'name' => $request->name,
        'description' => $request->description,
        'status' => $request->status,
        'priority' => $request->priority,
        ]);

        if ($request->parent_id) {
            $node = Task::find($request->parent_id);

            $node->appendNode($task);
        }

        return response()->json('Created');
    }

    /**
     * Display the specified resource.
     *
     * @param  App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
        $result = Task::whereDescendantOrSelf($task)->get()->toTree();
        return response()->json($result);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\TaskRequest  $request
     * @param  App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(TaskRequest $request, Task $task)
    {
        $task->update([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status,
            'priority' => $request->priority,
        ]);

        if ($request->parent_id) {
            $node = Task::find($request->parent_id);

            $node->appendNode($task);
        }

        Task::fixTree();

        return response()->json('Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        if ($task->status != 1) {
            $task->delete();
            return response()->json('Delete');
        } else {
            return response()->json('This task has been completed');
        }
    }

    /**
     * Find specified tasks.
     *
     * @param  Illuminate\Http\Request;  $request
     * @param  App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function fillter()
    {
        //
    }

    /**
     * Update the specified task status.
     *
     * @param  App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function completeTask(Task $task) 
    {
        $task_children = Task::whereDescendantOf($task)->get();
        foreach ($task_children as $task_status) {
            if ($task_status->status == 0) {
                return response()->json('You have unfinished tasks!');
            }
        }

        $task->update(['status' => 0]);

        return response()->json('Task complete!');
    }
}
