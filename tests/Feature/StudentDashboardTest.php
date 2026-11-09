<?php

use App\Models\User;
use Livewire\Livewire;

it('renders the dashboard when the student has no upcoming reservations', function () {
    $student = User::factory()->create([
        'role' => 'student',
        'university_id' => fake()->unique()->numerify('STUDENT-####'),
    ]);

    $this->actingAs($student);

    Livewire::test('student.dashboard')
        ->assertSee('No upcoming reservations');
});
