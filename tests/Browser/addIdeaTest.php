<?php

use App\Models\User;

it('creates a new idea', function () {
    $this->actingAs($user = User::factory()->create());

    visit(route('ideas.index'))
        ->click('@create-idea-button')
        ->fill('title', 'My Awesome Idea')
        ->click('@button-status-in-progress')
        ->fill('description', 'This is a description of my awesome idea.')
        ->fill('@new-link', 'https://example.com')
        ->click('@submit-new-link-button')
        ->fill('@new-step', 'Step one')
        ->click('@submit-new-step-button')
        ->click('Create')
        ->assertPathIs('/ideas')
        ->assertNoJavaScriptErrors()
        ->assertSee('My Awesome Idea');

    expect($idea = $user->ideas()->first())->toMatchArray([
        'title' => 'My Awesome Idea',
        'status' => 'in_progress',
        'description' => 'This is a description of my awesome idea.',
        'links' => ['https://example.com'],
    ]);
});
