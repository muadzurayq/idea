<?php

namespace App\Actions;

use App\Models\Idea;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CreateIdea
{

    public function __construct(protected ?User $user = null)
    {
        $this->user = $this->user ?? Auth::user();
    }
    public function handle(array $attributes): void
    {
        if (! $this->user) {
            throw new \RuntimeException('A user must be authenticated to create an idea.');
        }

        $data = collect($attributes)->only(['title', 'description', 'status', 'links'])->toArray();

        if (isset($attributes['image']) && $attributes['image'] instanceof \Illuminate\Http\UploadedFile) {
            $data['image_path'] = $attributes['image']->store('ideas', 'public');
        }

        DB::transaction(function () use ($data, $attributes) {
            $idea = $this->user->ideas()->create($data);

            $steps = collect($attributes['steps'] ?? [])
                ->map(fn (array $step) => [
                    'description' => trim((string) ($step['description'] ?? '')),
                    'completed' => (bool) ($step['completed'] ?? false),
                ])
                ->filter(fn (array $step) => $step['description'] !== '')
                ->values()
                ->all();

            if ($steps !== []) {
                $idea->steps()->createMany($steps);
            }
        });
    }

}
