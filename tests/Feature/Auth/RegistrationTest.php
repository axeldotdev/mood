<?php

test('registration screen can be rendered', function (): void {
    $response = $this->get(route('register'));

    $response->assertStatus(200);
});

test('new users can register', function (): void {
    $response = $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'password' => 'Xk9$mP2vL@nQ7wR!',
        'password_confirmation' => 'Xk9$mP2vL@nQ7wR!',
    ]);

    $response->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
});
