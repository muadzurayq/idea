<x-layout>
    <div class="py-8 max-w-4xl mx-auto">
        <div class="flex justify-between items-center">
            <a href="/ideas" class="flex items-center gap-x-2 text-sm font-medium text-foreground hover:underline">
                &larr; Back to Ideas
            </a>

            <div class="gap-x-3 flex items-center">
                <button
                    class="btn btn-outlined"
                    x-data
                    @click="$dispatch('open-modal', 'edit-idea')"
                    data-test="edit-idea-button"
                >
                    Edit Idea
                </button>
                <form action="{{ route('ideas.destroy',$idea) }}" method="POST">
                    @csrf
                    @method('DELETE')

                    <button class="text-red-500 text-sm">Delete</button>
                </form>
            </div>
        </div>

        <div class="mt-8 space-y-6">

            @if ($idea->image_path)
                <div>
                    <img src="{{ asset('storage/' . $idea->image_path) }}" alt="" class="w-full h-auto object-cover">
                </div>
            @endif
            {{-- العنوان --}}
            <h1 class="font-bold text-4xl">{{ $idea->title }}</h1>

            {{-- حالة الفكرة وتاريخ الإنشاء --}}
            <div class="mt-2 flex gap-x-3 items-center">
                <x-idea.status-label :status="$idea->status->value">
                    {{ $idea->status->label() }}
                </x-idea.status-label>

                <div class="text-muted-foreground text-sm">
                    {{ $idea->created_at->diffForHumans() }}
                </div>
            </div>


            <x-card class="mt-6">
                <div class="text-foreground max-w-none cursor-pointer">
                    {{ $idea->description }}
                </div>
            </x-card>

            @if ($idea->steps?->count())
                <div>
                    <h3 class="font-bold text-xl mt-6">steps</h3>

                    <div class="mt-3 space-y-2">
                        @foreach ($idea->steps as $step)
                            <x-card  class="text-primary font-medium flex gap-x-3 items-center">
                                <form method="post" action="{{route('steps.update',$step)}}" >
                                @csrf
                                @method('PATCH')
                                <button
                                    type="submit"
                                    role="checkbox"
                                    class="size-5 flex items-center justify-center rounded-md border transition-colors duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary/50 {{ $step->completed ? 'bg-primary border-primary text-primary-foreground' : 'border-input hover:border-primary text-transparent hover:text-muted-foreground' }}"
                                >
                                    <svg class="size-3.5 stroke-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </button>

                                </form>
                                <span class="{{ $step->completed ? 'line-through text-muted-forgraund' : '' }}">{{ $step->description }}</span>
                            </x-card>
                        @endforeach
                    </div>
                </div>
            @endif
            @if ($idea->links?->count())
                <div>
                    <h3 class="font-bold text-xl mt-6">Links</h3>

                    <div class="mt-3 space-y-2">
                        @foreach ($idea->links as $link)
                            <x-card :href="$link" class="text-primary font-medium flex gap-x-3 items-center">

                                <span>{{ $link }}</span>
                            </x-card>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
        <x-idea.model :idea="$idea" />
    </div>
</x-layout>
