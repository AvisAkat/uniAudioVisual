<?php

use App\Models\Equipment;
use App\Models\User;
use Livewire\Livewire;

it('creates equipment from the inventory modal', function () {
    $staff = User::factory()->create([
        'role' => 'staff',
        'university_id' => fake()->unique()->numerify('STAFF-####'),
    ]);

    $this->actingAs($staff);

    Livewire::test('staff.inventory')
        ->call('openCreateModal')
        ->set('name', 'Wireless Presentation Clicker')
        ->set('category', 'Projectors')
        ->set('totalQuantity', 4)
        ->set('condition', 'Good')
        ->set('description', 'Presentation remote for lecture rooms.')
        ->call('saveEquipment')
        ->assertHasNoErrors()
        ->assertSet('showModal', false);

    $equipment = Equipment::query()->firstOrFail();

    $this->assertModelExists($equipment);
    expect($equipment->asset_code)->not->toBeEmpty();
    expect($equipment->total_quantity)->toBe(4);
    expect($equipment->available_quantity)->toBe(4);
});

it('updates equipment and preserves the reserved quantity', function () {
    $staff = User::factory()->create([
        'role' => 'staff',
        'university_id' => fake()->unique()->numerify('STAFF-####'),
    ]);
    $equipment = Equipment::query()->create([
        'name' => 'Projector',
        'asset_code' => 'EQ-PROJECTOR1',
        'category' => 'Projectors',
        'description' => 'Old description.',
        'total_quantity' => 5,
        'available_quantity' => 3,
        'condition' => 'Good',
        'status' => 'Available',
    ]);

    $this->actingAs($staff);

    Livewire::test('staff.inventory')
        ->call('editEquipment', $equipment->id)
        ->assertSet('name', 'Projector')
        ->set('name', 'Lecture Projector')
        ->set('totalQuantity', 6)
        ->set('description', 'Updated description.')
        ->call('saveEquipment')
        ->assertHasNoErrors()
        ->assertSet('showModal', false);

    $equipment->refresh();

    expect($equipment->name)->toBe('Lecture Projector');
    expect($equipment->description)->toBe('Updated description.');
    expect($equipment->total_quantity)->toBe(6);
    expect($equipment->available_quantity)->toBe(4);
});

it('rejects a quantity below the currently reserved units', function () {
    $staff = User::factory()->create([
        'role' => 'staff',
        'university_id' => fake()->unique()->numerify('STAFF-####'),
    ]);
    $equipment = Equipment::query()->create([
        'name' => 'Projector',
        'asset_code' => 'EQ-PROJECTOR2',
        'category' => 'Projectors',
        'description' => 'Projector description.',
        'total_quantity' => 5,
        'available_quantity' => 3,
        'condition' => 'Good',
        'status' => 'Available',
    ]);

    $this->actingAs($staff);

    Livewire::test('staff.inventory')
        ->call('editEquipment', $equipment->id)
        ->set('totalQuantity', 1)
        ->call('saveEquipment')
        ->assertHasErrors(['totalQuantity' => 'Total quantity cannot be less than the 2 currently reserved units.']);

    $equipment->refresh();

    expect($equipment->total_quantity)->toBe(5);
    expect($equipment->available_quantity)->toBe(3);
});

it('forbids students from opening the equipment form', function () {
    $student = User::factory()->create([
        'role' => 'student',
        'university_id' => fake()->unique()->numerify('STUDENT-####'),
    ]);

    $this->actingAs($student);

    Livewire::test('staff.inventory')
        ->call('openCreateModal')
        ->assertForbidden();
});

it('shows the selected equipment in the three-slot availability modal', function () {
    $staff = User::factory()->create([
        'role' => 'staff',
        'university_id' => fake()->unique()->numerify('STAFF-####'),
    ]);
    $equipment = Equipment::query()->create([
        'name' => 'Lecture Projector',
        'asset_code' => 'EQ-LECTURE01',
        'category' => 'Projectors',
        'description' => 'Projector for lecture rooms.',
        'total_quantity' => 5,
        'available_quantity' => 3,
        'condition' => 'Good',
        'status' => 'Available',
    ]);

    $this->actingAs($staff);

    Livewire::test('staff.inventory')
        ->call('viewEquipment', $equipment->id)
        ->assertSet('viewingEquipmentId', $equipment->id)
        ->assertSee('Lecture Projector')
        ->assertSee('7:30 AM – 10:30 AM')
        ->assertSee('11:30 AM – 2:00 PM')
        ->assertSee('2:15 PM – 5:15 PM')
        ->assertSee('3');
});