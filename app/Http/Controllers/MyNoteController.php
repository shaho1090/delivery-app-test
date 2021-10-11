<?php

namespace App\Http\Controllers;

use App\Http\Requests\NoteStoreRequest;
use App\Http\Requests\NoteUpdateRequest;
use App\Http\Resources\NoteCollection;
use App\Http\Resources\NoteResource;
use App\Models\Note;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Response;

class MyNoteController extends Controller
{
    public function index(): JsonResponse
    {
        $notes = auth()->user()->notes()->orderBy('updated_at','desc')->paginate(10);

        $notesCollection = new NoteCollection($notes);

        return Response::json([
            'data' => $notesCollection,
            'pagination' => $notesCollection->pagination()
        ]);
    }

    public function store(NoteStoreRequest $request): JsonResponse
    {
        $request['author_id'] = auth()->id();

        $note = (new Note())->createNew($request->toArray());

        return Response::json([
            'data' => new NoteResource($note)
        ]);
    }

    public function update(Note $note, NoteUpdateRequest $request): JsonResponse
    {
        $note->update($request->toArray());

        $note->refresh();

        return Response::json([
            'data' => new NoteResource($note)
        ]);
    }

    public function show(Note $note): JsonResponse
    {
        return Response::json([
            'data' => new NoteResource($note)
        ]);
    }

    /**
     * @param Note $note
     * @return JsonResponse
     */
    public function destroy(Note $note): JsonResponse
    {
        $note->delete();

        return Response::json([
            'data' => 'The note has been successfully deleted!'
        ]);
    }
}
