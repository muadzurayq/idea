@props(['idea' => new \App\Models\Idea()])
<x-modal name="{{ $idea->exists ? 'edit-idea' : 'create-idea' }}"
         title="{{ $idea->exists ? 'Edit Idea' : 'Create Idea' }}">
    <form
        x-data="{
            status: @js(old('status', $idea->status?->value)),
            // تحويل الروابط القديمة إلى كائنات تحتوي على id و url لتجنب مشاكل الـ DOM
            links: @js(old('links', $idea->links) ?? []).map(link => ({ id: Math.random(), url: link })),
            newLink: '',
            steps: @js(old('steps', $idea->steps?->map->only('description', 'completed', 'id')) ?? []),
            newStep: '',

            reset() {
                this.status = @js(old('status', $idea->status?->value));
                this.links = @js(old('links', $idea->links) ?? []).map(link => ({ id: Math.random(), url: link }));
                this.newLink = '';
                this.steps = @js(old('steps', $idea->steps?->map->only('description', 'completed', 'id')) ?? []);
                this.newStep = '';
                this.$el.reset(); // clears Title and Description
            },

            addLink() {
                let value = this.newLink.trim();
                if (!value) return;
                if (!/^https?:\/\//i.test(value)) value = 'https://' + value;
                try { new URL(value); } catch { return; }

                // التحقق من عدم التكرار والتحقق من الـ url الداخلي
                if (!this.links.some(l => l.url === value)) {
                    this.links.push({ id: Math.random(), url: value });
                }
                this.newLink = '';
            },

            addStep() {
                let value = this.newStep.trim();
                if (!value) return;
                this.steps.push({
                    description: value,
                    completed: false,
                    id: null
                });
                this.newStep = '';
            }
        }"
        {{-- `show` comes from the parent modal component --}}
        x-init="$watch('show', value => { if (!value) reset() })"
        @submit="$el.enctype = $refs.imageInput.files.length ? 'multipart/form-data' : 'application/x-www-form-urlencoded'"
        action="{{ $idea->exists ? route('ideas.update', $idea) : route('ideas.store') }}"
        method="POST"
        class="space-y-6"
    >
    >
        @csrf
        @if($idea->exists)
            @method('PATCH')
        @endif

        <div class="space-y-6">
            {{-- Title --}}
            <x-form.field
                label="Title"
                name="title"
                placeholder="Enter an idea for your title"
                autofocus
                required
                value="{{ $idea->title }}"
            />

            {{-- Status --}}
            <div class="space-y-2">
                <span class="label">Status</span>

                <div class="flex gap-x-3">
                    @foreach (\App\IdeaStatus::cases() as $status)
                        <button
                            type="button"
                            dusk="button-status-{{ str($status->value)->replace('_', '-') }}"
                            data-testid="button-status-{{ str($status->value)->replace('_', '-')->toString() }}"
                            @click="status = @js($status->value)"
                            class="btn flex-1 h-10"
                            :class="{ 'btn-outlined': status !== @js($status->value) }"
                        >
                            {{ $status->label() }}
                        </button>
                    @endforeach
                </div>

                <input type="hidden" name="status" :value="status">

                <x-form.error name="status"/>
            </div>

            {{-- Description --}}
            <x-form.field
                label="Description"
                name="description"
                type="textarea"
                placeholder="Describe your idea..."
                value="{{ $idea->description }}"
            />

            {{-- Featured Image --}}
            <div class="space-y-3">
                <label for="image" class="label">Featured Image</label>

                @if($idea->image_path)
                    <div class="space-y-2">
                        {{-- حاوية الصورة وحدها --}}
                        <div class="w-full h-48 overflow-hidden rounded-lg">
                            <img src="{{ asset('storage/' . $idea->image_path) }}" alt="{{ $idea->title }}"
                                 class="w-full h-full object-cover">
                        </div>

                        <button type="submit" form="change-image" class="btn btn-outlined w-full">
                            remove image
                        </button>
                    </div>
                @endif
                <input x-ref="imageInput" type="file" name="image" accept="image/*">
                <x-form.error name="image"/>
            </div>


            {{-- Steps --}}
            <fieldset class="space-y-3">
                <legend class="label">Steps</legend>

                <template x-for="(step, index) in steps" :key="step.id || index">
                    <div class="flex gap-x-2 items-center">
                        {{-- إذا كان هناك ID للخطوة يتم إرساله --}}
                        <template x-if="step.id">
                            <input type="hidden" :name="`steps[${index}][id]`" :value="step.id">
                        </template>

                        <input :name="`steps[${index}][description]`" x-model="step.description" class="input flex-1">
                        <input type="hidden" :name="`steps[${index}][completed]`" :value="step.completed ? '1' : '0'">

                        <button
                            type="button"
                            aria-label="Remove step"
                            @click="steps.splice(index, 1)"
                            class="form-muted-icon"
                        >
                            <x-icons.close/>
                        </button>
                    </div>
                </template>

                <div class="flex gap-x-2 items-center">
                    <input
                        x-model="newStep"
                        id="newStep"
                        dusk="new-step"
                        data-testid="new-step"
                        placeholder="What needs to be done?"
                        class="input flex-1"
                        spellcheck="false"
                        @keydown.enter.prevent="addStep()"
                    >

                    <button
                        type="button"
                        dusk="submit-new-step-button"
                        data-testid="submit-new-step-button"
                        @click="addStep()"
                        :disabled="!newStep || !newStep.trim()"
                        aria-label="Add a new step"
                    >
                        <x-icons.close class="rotate-45"/>
                    </button>
                </div>
            </fieldset>

            {{-- Links --}}
            <fieldset class="space-y-3">
                <legend class="label">Links</legend>

                <template x-for="(link, index) in links" :key="link.id || index">
                    <div class="flex gap-x-2 items-center">
                        {{-- name="links[]" يضمن إرسال الرابط كمصفوفة نصية صحيحة للسيرفر --}}
                        <input name="links[]" x-model="link.url" class="input flex-1">

                        <button
                            type="button"
                            aria-label="Remove link"
                            @click="links.splice(index, 1)"
                            class="form-muted-icon"
                        >
                            <x-icons.close/>
                        </button>
                    </div>
                </template>

                <div class="flex gap-x-2 items-center">
                    <input
                        x-model="newLink"
                        type="text"
                        inputmode="url"
                        id="new-link"
                        dusk="new-link"
                        data-testid="new-link"
                        placeholder="https://example.com"
                        autocomplete="url"
                        class="input flex-1"
                        spellcheck="false"
                        @keydown.enter.prevent="addLink()"
                    >

                    <button
                        type="button"
                        dusk="submit-new-link-button"
                        data-testid="submit-new-link-button"
                        @click="addLink()"
                        :disabled="!newLink || !newLink.trim()"
                        aria-label="Add a new link"
                    >
                        <x-icons.close class="rotate-45"/>
                    </button>
                </div>
            </fieldset>
        </div>

        {{-- Footer (must be last) --}}
        <div class="flex justify-end gap-x-5 mt-6">
            <button type="button" @click="show = false" class="btn btn-outlined">
                Cancel
            </button>
            <button type="submit" dusk="submit-create-idea-button" data-testid="submit-create-idea-button" class="btn btn-primary">
                {{ $idea->exists ? 'Update' : 'Create' }}
            </button>
        </div>
    </form>

    @if($idea->exists && $idea->image_path)
        <form action="{{ route('ideas.destroy-image', $idea) }}" method="POST" id="change-image">
            @csrf
            @method('DELETE')
        </form>
    @endif

</x-modal>
