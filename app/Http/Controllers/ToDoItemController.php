<?php

namespace App\Http\Controllers;

use App\ToDoItem;
use Illuminate\Http\Request;
use App\Http\Resources\ToDoItem as ToDoItemResource;

class ToDoItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(string $status = "")
    {
        $user = auth('api')->user();

        if ($status === "incomplete" || $status === "completed") {
            $isCompleted = ($status === "incomplete") ? 0 : 1;
            $toDoItems = $user->toDoItems()->where('completed', $isCompleted);
        } else if ($status === "") {
            $toDoItems = $user->toDoItems();
        } else {
            return response()->json([
                'error' => 'Incorrect parameters supplied.'
            ], 400);
        }

        return ToDoItemResource::collection($toDoItems->paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = auth('api')->user();

        $request->validate([
            'body' => 'required|string|max:191'
        ]);

        $toDoItem = new ToDoItem();
        $toDoItem->owner_id = $user->id;
        $toDoItem->body = $request->input('body');
        $toDoItem->save();

        return new ToDoItemResource($toDoItem);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ToDoItem  $toDoItem
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ToDoItem $toDoItem)
    {
        $user = auth('api')->user();

        // If the author of the to do item is the current user, update it.
        if ($toDoItem->owner_id === $user->id) {
            $request->validate([
                'body' => 'required_without:completed|string|max:191',
                'completed' => 'required_without:body|in:true,false,1,0'
            ]);

            // Cast the string value to a PHP boolean data type.
            $completed = ($request->input('completed') === 'true' || $request->input('completed') === '1') ? true : false;

            if (($request->has('body'))) { $toDoItem->body = $request->input('body'); }
            if (($request->has('completed'))) { $toDoItem->completed = $completed; }

            $toDoItem->updated_at = now();
            $toDoItem->save();

            return new ToDoItemResource($toDoItem);
        } else {
            return response()->json([
                'error' => 'You are not authorised to update this post.'
            ], 401);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ToDoItem  $toDoItem
     * @return \Illuminate\Http\Response
     */
    public function destroy(ToDoItem $toDoItem)
    {
        $user = auth('api')->user();

        // If the author of the to do item is the current user, delete it.
        if ($toDoItem->owner_id === $user->id) {
            $toDoItem->delete();
            return response()->json([
               'success' => 'To do item has been deleted.'
            ], 200);
        } else {
            return response()->json([
               'error' => 'You are not authorised to delete this post.'
            ], 401);
        }
    }
}
