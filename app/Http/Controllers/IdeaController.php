<?php

namespace App\Http\Controllers;

use App\Actions\CreateIdea;
use App\Actions\UpdateIdea;
use App\Http\Requests\IdeaRequest;
use App\IdeaStatus;
use App\Models\Idea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use function Amp\Dns\query;

class IdeaController extends Controller
{
    //
    public function index(request $request)
    {
        $user =Auth::user();


        $idea = $user
            ->ideas()
            ->when($request->status ,fn($query,$status) => $query->where('status', $status))
            ->latest()
            ->get();

        return view('ideas/index',[
            'ideas'=>$idea ,
            'statusCount' => Idea::statusesCount($user)

        ]);
    }

    public function show(Idea $idea)
    {
        Gate::authorize('view', $idea);

        return view('ideas/show',[
            'idea'=>$idea

            ]
        );
    }

    public function store(IdeaRequest $request , CreateIdea $action)
    {

        $action->handle($request->safe()->all());

        return redirect()->route('ideas.index');
    }

    public function destroy(Idea $idea)
    {
        Gate::authorize('view', $idea);


        Auth::user()->ideas()->find($idea->id)->delete();

        return redirect()->route('ideas.index');
    }

    public function update(IdeaRequest $request, Idea $idea, UpdateIdea $action)
    {
        Gate::authorize('view', $idea);

        $action->handle($request->validated(), $idea);

        return redirect()->route('ideas.index');
    }

}
