<?php

use App\Models\Idea;
use App\Models\User;

it('can add and remove dynamic links in the create idea modal', function (): void {
    $email = 'links-'.uniqid().'@example.com';

    // 1. Authenticate a user by registering via browser (so data is visible to the browser server)
    visit('/register')
        ->fill('name', 'Links Tester')
        ->fill('email', $email)
        ->fill('password', 'password')
        ->click('Create Account')
        ->assertPathIs('/ideas')
        // 2. Open the create idea modal
        ->click('New Idea')
        ->waitForText('Title')
        // 3. Fill the title, description, and status
        ->fill('title', 'Browser Dynamic Links Idea')
        ->fill('description', 'Testing the links via browser.')
        ->click('In Progress')
        // 4. Fill the new-link input with a valid first URL.
        ->fill('[data-test="new-link"]', 'https://laravel.com')
        // 5. Click the add-link button.
        ->click('[data-test="submit-new-link-button"]')
        // 6. Fill the new-link input with a second valid URL.
        ->fill('[data-test="new-link"]', 'https://livewire.laravel.com')
        // 7. Click the add-link button again.
        ->click('[data-test="submit-new-link-button"]')
        // Add a third link and test removing it before submission
        ->fill('[data-test="new-link"]', 'https://remove-me.com')
        ->click('[data-test="submit-new-link-button"]')
        ->click('[data-test="remove-link-2"]') // Remove the third link (index 2)
        // 8. Submit the form.
        ->click('Create Idea')
        // 9. Assert the redirect to the Ideas index.
        ->waitForText('Idea created.')
        ->assertPathIs('/ideas');

    $idea = Idea::where('title', 'Browser Dynamic Links Idea')->first();
    $user = User::where('email', $email)->first();

    expect($idea)->not->toBeNull()
        ->and($idea->user_id)->toBe($user->id)
        ->and(count($idea->links))->toBe(2)
        ->and($idea->links[0])->toBe('https://laravel.com')
        ->and($idea->links[1])->toBe('https://livewire.laravel.com');
});
