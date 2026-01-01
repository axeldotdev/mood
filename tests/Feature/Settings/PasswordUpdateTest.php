<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Volt\Volt;

test('password can be updated', function (): void {
    $user = User::factory()->create([
        'password' => Hash::make('password'),
    ]);

    $this->actingAs($user);

    $response = Volt::test('settings.password')
        ->set('current_password', 'password')
        ->set('password', 'Xk9#mLz$2vQr!')
        ->set('password_confirmation', 'Xk9#mLz$2vQr!')
        ->call('updatePassword');

    $response->assertHasNoErrors();

    expect(Hash::check('Xk9#mLz$2vQr!', $user->refresh()->password))->toBeTrue();
});

test('correct password must be provided to update password', function (): void {
    $user = User::factory()->create([
        'password' => Hash::make('password'),
    ]);

    $this->actingAs($user);

    $response = Volt::test('settings.password')
        ->set('current_password', 'wrong-password')
        ->set('password', 'Xk9#mLz$2vQr!')
        ->set('password_confirmation', 'Xk9#mLz$2vQr!')
        ->call('updatePassword');

    $response->assertHasErrors(['current_password']);
});
