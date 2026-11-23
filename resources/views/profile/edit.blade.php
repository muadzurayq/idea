<x-layout>
    <x-form title="Edit your account" description="start tracking your ideas today">
        <form action="/profile/edit" method="POST" class="mt-10 space-y-4">
            @csrf
            @method('PATCH')
            <x-form.field name="name" label="Name" value="{{ old('name', $user->name) }}" />
            <x-form.field name="email" label="Email" type="email" value="{{ old('email', $user->email) }}" />
            <x-form.field name="password" label="Password" type="password"/>

            <button type="submit" class="btn mt-2 h-10 w-full">Edit Account</button>
        </form>
    </x-form>
</x-layout>
