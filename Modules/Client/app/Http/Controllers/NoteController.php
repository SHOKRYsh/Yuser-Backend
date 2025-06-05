<?php

namespace Modules\Client\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Client\Models\Note;

class NoteController extends Controller
{
    public function index(Request $request)
    {
        $query = Note::with(['sender', 'reciever']);

        if ($request->filled('sender_id')) {
            $query->where('sender_id', $request->sender_id);
        }

        if ($request->filled('reciever_id')) {
            $query->where('reciever_id', $request->reciever_id);
        }

        return $this->respondOk($query->paginate());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'reciever_id' => 'required|exists:users,id',
            'note'        => 'required|string',
        ]);

        $note = Note::create([
            'sender_id'   => $request->user()->id,
            'reciever_id' => $validated['reciever_id'],
            'note'        => $validated['note'],
        ]);

        return $this->respondCreated($note, 'Note created successfully.');
    }

    public function show($id)
    {
        $note = Note::with(['sender', 'reciever'])->find($id);

        if (!$note) {
            return $this->respondNotFound(null, 'Note not found.');
        }

        return $this->respondOk($note);
    }
}
