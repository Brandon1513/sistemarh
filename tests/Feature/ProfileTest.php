<?php

use App\Models\User;

test('profile page is displayed', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get('/profile');

    $response->assertOk();
});

test('profile information can be updated', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $user->refresh();

    $this->assertSame('Test User', $user->name);
    $this->assertSame('test@example.com', $user->email);
    $this->assertNull($user->email_verified_at);
});

test('email verification status is unchanged when the email address is unchanged', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => 'Test User',
            'email' => $user->email,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $this->assertNotNull($user->refresh()->email_verified_at);
});
test('los empleados no pueden borrar su propia cuenta', function () {
    $user = User::factory()->create();

    // Las bajas las hace RH inactivando al usuario; la ruta para borrarse ya no existe
    $this
        ->actingAs($user)
        ->delete('/profile', ['password' => 'password'])
        ->assertStatus(405);

    $this->assertNotNull($user->fresh());
});
test('no existe el registro público de usuarios', function () {
    // Las altas las hace RH desde Gestionar Usuarios
    $this->get('/register')->assertNotFound();
});