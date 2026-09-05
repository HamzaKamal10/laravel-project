<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Idea;
use App\Http\Requests\StoreIdeaRequest;
use App\Http\Requests\UpdateIdeaRequest;
use Illuminate\Http\Request;

class IdeaController extends Controller
{
    public function index()
    {
        $ideas = Idea::latest()->get();

        return view('welcome', compact('ideas'));
    }

    public function store(StoreIdeaRequest $request)
    {
        $data = $request->validated();
        $data['status'] = 'Pending';

        Idea::create($data);

        return redirect('/');
    }

    public function show(Idea $idea)
    {
        return response()->json($idea);
    }

    public function update(UpdateIdeaRequest $request, Idea $idea)
    {
        $idea->update($request->validated());

        return response()->json($idea);
    }

    public function destroy(Idea $idea)
    {
        $idea->delete();

        return redirect('/');
    }
}
