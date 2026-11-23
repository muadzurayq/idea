<x-layout>
    <div>
        <header class="py-8 md:py-12">
            <h1 class="text-3xl font-bold">Ideas</h1>
            <p class="text-muted-foreground text-sm mt-2">Capture your thoughts. Make a plan.</p>

            <x-card
                x-data
                dusk="create-idea-button"
                data-testid="create-idea-button"
                @click="$dispatch('open-modal', 'create-idea')"
                is="button"
                type="button"
                class="mt-10 cursor-pointer h-32 w-full text-left"
            >
                <p>What's the idea?</p>
            </x-card>
        </header>

        <div>
            {{-- Status filter buttons --}}
            <div class="flex flex-wrap gap-2">
                <a href="/ideas"
                   class="btn {{ !request()->has('status') ? '' : 'btn-outlined' }}">
                    All ({{ $statusCount['all'] ?? 0 }})
                </a>

                @foreach (\App\IdeaStatus::cases() as $status)
                    <a href="/ideas?status={{ $status->value }}"
                       class="btn {{ request('status') === $status->value ? '' : 'btn-outlined' }}">
                        {{ $status->label() }} ({{ $statusCount[$status->value] ?? 0 }})
                    </a>
                @endforeach
            </div>

            {{-- Ideas list --}}
            <div class="mt-10">
                <div class="grid md:grid-cols-2 gap-6">
                    @forelse($ideas as $idea)
                        <x-card href="{{ route('ideas.show', $idea) }}">
                            @if ($idea->image_path)
                                {{-- وضعنا overflow-hidden و rounded هنا للتحكم بحدود الصورة --}}
                                <div class="mb-4 -mx-6 -mt-6 overflow-hidden rounded-t-lg">
                                    <img src="{{ asset('storage/' . $idea->image_path) }}"
                                         alt=""
                                         class="w-full h-48 object-cover">
                                </div>
                            @endif

                            <h3 class="text-foreground text-lg font-bold">{{ $idea->title }}</h3>

                            <div class="mt-1">
                                <x-idea.status-label :status="$idea->status->value">
                                    {{ $idea->status->label() }}
                                </x-idea.status-label>
                            </div>

                            <div class="mt-5 text-muted-foreground line-clamp-3">
                                {{ $idea->description }}
                            </div>

                            <div class="mt-4 text-xs text-muted-foreground">
                                {{ $idea->created_at?->diffForHumans() }}
                            </div>
                        </x-card>
                    @empty
                        <div class="col-span-full">
                            <x-card>
                                <p class="text-center text-muted-foreground py-4">
                                    No ideas found.
                                </p>
                            </x-card>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
        {{-- Create idea modal --}}
            <x-idea.model />
    </div>
</x-layout>
