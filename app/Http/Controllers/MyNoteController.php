<?php

namespace App\Http\Controllers;

use App\Http\Requests\NoteStoreRequest;
use App\Http\Resources\NoteResource;
use App\Models\Note;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;

class MyNoteController extends Controller
{
    public function store(NoteStoreRequest $request): JsonResponse
    {
        $request['author_id'] = auth()->id();

        $note = (new Note())->createNew($request->toArray());

        return Response::json([
            'data' => new NoteResource($note)
        ]);
    }
}
