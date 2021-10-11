<?php

namespace App\Http\Controllers;

use App\Http\Requests\NoteStoreRequest;
use App\Models\Note;
use Illuminate\Http\Request;

class MyNoteController extends Controller
{
    public function store(NoteStoreRequest $request)
    {
        $request['author_id'] = auth()->id();

        $note = (new Note())->createNew($request->toArray());

    }
}
