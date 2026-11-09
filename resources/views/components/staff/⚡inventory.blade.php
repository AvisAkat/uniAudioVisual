<?php

use App\Models\ActivityLog;
use App\Models\Equipment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    const loaderActions = [
        'saveEquipment',
    ];

    public bool $showModal = false;

    public ?int $editingEquipmentId = null;

    public ?int $viewingEquipmentId = null;

    public ?int $deletingEquipmentId = null;

    public string $name = '';

    public string $category = 'Projectors';

    public int $totalQuantity = 1;

    public string $condition = 'Good';

    public string $description = '';

    public string $search = '';

    public string $categoryFilter = 'All';

    public string $conditionFilter = 'All';

    public string $stockDate = '';

    public string $stockSlot = 'slot-1';

    public function mount(): void
    {
        $this->stockDate = now()->toDateString();
    }

    #[Computed]
    public function equipmentItems(): Collection
    {
        return Equipment::query()
            ->when($this->search !== '', function (Builder $query): void {
                $searchTerm = '%'.$this->search.'%';

                $query->where(function (Builder $query) use ($searchTerm): void {
                    $query->where('name', 'like', $searchTerm)
                        ->orWhere('description', 'like', $searchTerm)
                        ->orWhere('asset_code', 'like', $searchTerm);
                });
            })
            ->when($this->categoryFilter !== 'All', fn (Builder $query) => $query->where('category', $this->categoryFilter))
            ->when($this->conditionFilter !== 'All', fn (Builder $query) => $query->where('condition', $this->conditionFilter))
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function stockSlots(): array
    {
        return [
            'slot-1' => '7:30 AM – 10:30 AM',
            'slot-2' => '11:00 AM – 2:00 PM',
            'slot-3' => '2:15 PM – 5:15 PM',
        ];
    }

    #[Computed]
    public function selectedStockSlot(): string
    {
        return $this->stockSlots[$this->stockSlot] ?? $this->stockSlots['slot-1'];
    }

    public function resetFilters(): void
    {
        $this->reset('search', 'categoryFilter', 'conditionFilter');
        $this->stockDate = now()->toDateString();
        $this->stockSlot = 'slot-1';
        unset($this->equipmentItems, $this->selectedStockSlot);
    }

    public function openCreateModal(): void
    {
        $this->authorizeStaff();
        $this->resetForm();
        $this->showModal = true;
    }

    public function editEquipment(int $equipmentId): void
    {
        $this->authorizeStaff();

        $equipment = Equipment::query()->findOrFail($equipmentId);

        $this->editingEquipmentId = $equipment->id;
        $this->name = $equipment->name;
        $this->category = $equipment->category;
        $this->totalQuantity = $equipment->total_quantity;
        $this->condition = $equipment->condition;
        $this->description = $equipment->description ?? '';
        $this->resetValidation();
        $this->showModal = true;
    }

    public function viewEquipment(int $equipmentId): void
    {
        $this->authorizeStaff();

        $equipment = Equipment::query()->findOrFail($equipmentId);
        $this->viewingEquipmentId = $equipment->id;
        unset($this->viewedEquipment);
    }

    #[Computed]
    public function viewedEquipment(): ?Equipment
    {
        return $this->viewingEquipmentId === null
          ? null
          : Equipment::query()->find($this->viewingEquipmentId);
    }

    public function closeEquipmentView(): void
    {
        $this->viewingEquipmentId = null;
        unset($this->viewedEquipment);
    }

    public function openDeleteEquipment(int $equipmentId): void
    {
        $this->authorizeStaff();

        Equipment::query()->findOrFail($equipmentId);

        $this->deletingEquipmentId = $equipmentId;
        unset($this->equipmentToDelete);
    }

    #[Computed]
    public function equipmentToDelete(): ?Equipment
    {
        return $this->deletingEquipmentId === null
          ? null
          : Equipment::query()->find($this->deletingEquipmentId);
    }

    public function closeDeleteModal(): void
    {
        $this->deletingEquipmentId = null;
        unset($this->equipmentToDelete);
    }

    public function deleteEquipment(): void
    {
        $this->authorizeStaff();

        $equipment = $this->equipmentToDelete;

        if ($equipment === null) {
            $this->closeDeleteModal();

            return;
        }

        $equipmentName = $equipment->name;

        $deleted = DB::transaction(function () use ($equipment): bool {
            if (! $equipment->delete()) {
                return false;
            }

            ActivityLog::record(
                'Equipment deleted',
                'Equipment',
                "Removed {$equipment->name} ({$equipment->asset_code}) from inventory.",
                $equipment,
            );

            return true;
        });

        if (! $deleted) {
            $this->dispatch(
                'toast',
                title: 'Something went wrong',
                message: 'Unable to remove the equipment item. Please try again later.',
                type: 'error'
            );

            return;
        }

        $this->closeDeleteModal();
        unset($this->equipmentItems);

        $this->dispatch(
            'toast',
            title: 'Equipment removed',
            message: "$equipmentName has been removed from the inventory.",
            type: 'success'
        );
    }

    public function saveEquipment(): void
    {
        $this->authorizeStaff();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', Rule::in(['Projectors', 'HDMI Cables', 'Extension Boards', 'Audio Systems'])],
            'totalQuantity' => ['required', 'integer', 'min:0'],
            'condition' => ['required', 'string', Rule::in(['Good', 'Under Maintenance', 'Damaged'])],
            'description' => ['required', 'string', 'max:2000'],
        ]);

        $this->editingEquipmentId === null
          ? $isEdit = false
          : $isEdit = true;

        $equipment = $this->editingEquipmentId === null
          ? new Equipment
          : Equipment::query()->findOrFail($this->editingEquipmentId);

        $reservedQuantity = $equipment->exists
          ? max(0, $equipment->total_quantity - $equipment->available_quantity)
          : 0;
        $newTotalQuantity = (int) $validated['totalQuantity'];

        if ($newTotalQuantity < $reservedQuantity) {
            $this->addError('totalQuantity', "Total quantity cannot be less than the {$reservedQuantity} currently reserved units.");

            return;
        }

        $equipment->fill([
            'name' => $validated['name'],
            'category' => $validated['category'],
            'description' => $validated['description'],
            'total_quantity' => $newTotalQuantity,
            'available_quantity' => $newTotalQuantity - $reservedQuantity,
            'condition' => $validated['condition'],
        ]);

        if (! $equipment->exists) {
            $equipment->asset_code = 'EQ-'.Str::upper(Str::random(10));
        }

        $saved = DB::transaction(function () use ($equipment, $isEdit, $newTotalQuantity): bool {
            if (! $equipment->save()) {
                return false;
            }

            ActivityLog::record(
                $isEdit ? 'Equipment updated' : 'Equipment created',
                'Equipment',
                $isEdit
                  ? "Updated {$equipment->name}; total quantity is {$newTotalQuantity} and condition is {$equipment->condition}."
                  : "Added {$equipment->name} ({$equipment->category}) with {$newTotalQuantity} unit(s).",
                $equipment,
            );

            return true;
        });

        if (! $saved) {
            $this->dispatch(
                'toast',
                title: 'Something went wrong',
                message: 'Unable to save the equipment item. Please try again later.',
                type: 'error'
            );

            return;
        } else {
            $this->resetForm();
            $this->showModal = false;
            unset($this->equipmentItems);

            $this->dispatch(
                'toast',
                title: 'Success',
                message: ! $isEdit
                ? 'New equipment item has been added to the inventory.'
                : 'Equipment item has been updated successfully.',
                type: 'success'
            );
        }

    }

    public function closeModal(): void
    {
        $this->resetForm();
        $this->showModal = false;
    }

    private function resetForm(): void
    {
        $this->reset('editingEquipmentId', 'name', 'totalQuantity', 'condition', 'description');
        $this->category = 'Projectors';
        $this->resetValidation();
    }

    private function authorizeStaff(): void
    {
        abort_unless(in_array(auth()->user()?->role, ['staff', 'admin'], true), 403);
    }
};
?>

<div>
  <section class="app-view is-entering" id="view-inventory" aria-label="Inventory management">
    <div id="inventory-content">
      <div class="section-heading">
        <div>
          <h2>Equipment inventory</h2>
          <p>Inventory view for <strong>{{ $stockDate }}</strong>, <strong>{{ $this->selectedStockSlot }}</strong>.</p>
        </div>
        <div class="section-actions"><button class="button button-primary" type="button"
            wire:click="openCreateModal"><svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
              <path d="M12 5v14M5 12h14"></path>
            </svg> Add equipment</button></div>
      </div>
      <div class="alert"><svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
          <circle cx="12" cy="12" r="9"></circle>
          <path d="M12 11v6M12 7h.01"></path>
        </svg>
        <div><strong>Reservation-safe stock controls</strong>
          <p>Quantity cannot be reduced below the highest active reservation in any single slot. Maintenance and damaged
            conditions prevent new bookings.</p>
        </div>
      </div>
      <div class="filter-bar">
        <div class="search-control"><span><svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
              <circle cx="11" cy="11" r="7"></circle>
              <path d="m20 20-4-4"></path>
            </svg></span><input id="inventory-search" type="search" value="" placeholder="Search equipment inventory"
            aria-label="Search inventory" wire:model.live.debounce.300ms="search"></div>
        <div class="control-group"><label for="inventory-category">Category</label><select id="inventory-category"
            wire:model.live="categoryFilter">
            <option value="All">All categories</option>
            <option value="Projectors">Projectors</option>
            <option value="HDMI Cables">HDMI Cables</option>
            <option value="Extension Boards">Extension Boards</option>
            <option value="Audio Systems">Audio Systems</option>
          </select></div>
        <div class="control-group"><label for="inventory-condition">Condition</label><select id="inventory-condition"
            wire:model.live="conditionFilter">
            <option value="All">All conditions</option>
            <option>Good</option>
            <option>Under Maintenance</option>
            <option>Damaged</option>
          </select></div>
        <div class="control-group"><label for="inventory-date">Stock date</label><input id="inventory-date" type="date"
            wire:model.live="stockDate"></div>
        <div class="control-group"><label for="inventory-slot">Stock time</label><select id="inventory-slot"
            wire:model.live="stockSlot">
            @foreach ($this->stockSlots as $slotValue => $slotLabel)
              <option value="{{ $slotValue }}">{{ $slotLabel }}</option>
            @endforeach
          </select></div><button class="button button-outline" type="button" wire:click="resetFilters">Reset</button>
      </div>
      <div class="results-meta"><span>{{ $this->equipmentItems->count() }} equipment type(s) ·
          {{ $this->equipmentItems->sum('total_quantity') }} total units</span><span>Reserved = total quantity minus
          available quantity</span></div>
      <div class="table-card">
        <div class="table-wrap">
          <table class="data-table responsive-table">
            <thead>
              <tr>
                <th>Equipment Name</th>
                <th>Category</th>
                <th>Total Quantity</th>
                <th>Currently Reserved</th>
                <th>Available Quantity</th>
                <th>Condition</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($this->equipmentItems as $equipment)
                <tr wire:key="equipment-{{ $equipment->id }}">
                  <td data-label="Equipment">
                    <div class="inventory-name">
                      <span class="inventory-icon"><svg class="icon" viewBox="0 0 24 24" aria-hidden="true">
                          <rect x="3" y="7" width="18" height="11" rx="2"></rect>
                          <path d="M7 11h10M7 14h6M6 18v2M18 18v2"></path>
                        </svg>
                      </span>
                      <span>
                        <span class="cell-primary">{{ $equipment->name }}</span>
                        <span class="cell-secondary truncate">{{ $equipment->description }}</span>
                        <span class="cell-secondary">Asset code: {{ $equipment->asset_code }}</span>
                      </span>
                    </div>
                  </td>
                  <td data-label="Category"><span class="category-badge">{{ $equipment->category }}</span></td>
                  <td data-label="Total quantity"><strong>{{ $equipment->total_quantity }}</strong> units</td>
                  <td data-label="Currently reserved">
                    <strong>{{ $equipment->total_quantity - $equipment->available_quantity }}</strong> units
                  </td>
                  <td data-label="Available"><strong class="text-success">{{ $equipment->available_quantity }}</strong>
                    units</td>
                  <td data-label="Condition"><span
                      class="condition-badge status-{{ strtolower($equipment->condition) }}">{{ $equipment->condition }}</span>
                  </td>
                  <td data-label="Actions">
                    <div class="table-actions">
                      <button class="icon-button" type="button" wire:click="viewEquipment({{ $equipment->id }})"
                        title="View equipment availability"><svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                          <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"></path>
                          <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                      </button>
                      <button class="icon-button" type="button" wire:click="editEquipment({{ $equipment->id }})"
                        title="Edit equipment">
                        <svg class="icon" viewBox="0 0 24 24" aria-hidden="true">
                          <path d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z"></path>
                        </svg>
                      </button>
                      <button class="icon-button action-delete" type="button" wire:click="openDeleteEquipment({{ $equipment->id }})"
                        data-action="delete-equipment" data-id="{{ $equipment->id }}" title="Remove equipment"><svg class="icon "
                          viewBox="0 0 24 24" aria-hidden="true">
                          <path d="M3 6h18M8 6V3h8v3M19 6l-1 15H6L5 6M10 11v5M14 11v5"></path>
                        </svg></button>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7">No equipment has been added yet.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>

  @if ($showModal)
    {{-- Add/Edit equipment modal --}}
    <div class="modal-backdrop" id="modal-backdrop">
      <div class="modal " id="modal" role="dialog" aria-modal="true" aria-labelledby="modal-title">
        <div id="modal-content">
          <form id="equipment-form" wire:submit="saveEquipment">
            <div class="modal-header">
              <div class="modal-heading"><span class="modal-heading-icon "><svg class="icon " viewBox="0 0 24 24"
                    aria-hidden="true">
                    <path d="M12 5v14M5 12h14"></path>
                  </svg></span>
                <div>
                  <h2 id="modal-title">{{ $editingEquipmentId ? 'Edit equipment' : 'Add new equipment' }}</h2>
                  <p>
                    {{ $editingEquipmentId ? 'Update this inventory item.' : 'Add a new item to the department inventory.' }}
                  </p>
                </div>
              </div><button class="icon-button modal-close" type="button" wire:click="closeModal"
                aria-label="Close dialog"><svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                  <path d="M18 6 6 18M6 6l12 12"></path>
                </svg></button>
            </div>
            <div class="modal-body">
              <div class="field-group"><label for="equipment-name">Equipment name <span
                    class="required">*</span></label><input id="equipment-name" wire:model="name" required
                  placeholder="e.g. Wireless Presentation Clicker">
                @error('name') <span class="field-error">{{ $message }}</span> @enderror
              </div>
              <div class="form-grid two-columns">
                <div class="field-group"><label for="equipment-category-form">Category <span
                      class="required">*</span></label><select id="equipment-category-form" wire:model="category"
                    required>
                    <option value="Projectors">Projectors</option>
                    <option value="HDMI Cables">HDMI Cables</option>
                    <option value="Extension Boards">Extension Boards</option>
                    <option value="Audio Systems">Audio Systems</option>
                  </select></div>
                <div class="field-group"><label for="equipment-total">Total quantity <span
                      class="required">*</span></label><input id="equipment-total" wire:model="totalQuantity"
                    type="number" min="0" step="1" required>
                  @error('totalQuantity') <span class="field-error">{{ $message }}</span> @enderror
                </div>
              </div>
              <div class="field-group"><label for="equipment-condition">Equipment condition</label><select
                  id="equipment-condition" wire:model="condition">
                  <option>Good</option>
                  <option>Under Maintenance</option>
                  <option>Damaged</option>
                </select></div>
              <div class="field-group"><label for="equipment-description">Short description <span
                    class="required">*</span></label><textarea id="equipment-description" wire:model="description"
                  required placeholder="Describe key features and typical academic use."></textarea>
                @error('description') <span class="field-error">{{ $message }}</span> @enderror
              </div>


            </div>
            <div class="modal-footer"><button class="button button-outline" type="button"
                wire:click="closeModal">Cancel</button><button class="button button-primary" type="submit"
                wire:loading.attr="disabled"><svg class="icon " viewBox="0 0 24 24" aria-hidden="true">
                  <path d="m5 12 4 4L19 6"></path>
                </svg> {{ $editingEquipmentId ? 'Save changes' : 'Add equipment' }}</button></div>
          </form>
        </div>
      </div>
    </div>
  @endif

  @if ($viewingEquipmentId !== null && $this->viewedEquipment)
    {{-- View Equipment modal --}}
    <div class="modal-backdrop" id="equipment-view-backdrop">
      <div class="modal modal-large" id="equipment-view-modal" role="dialog" aria-modal="true"
        aria-labelledby="equipment-view-title">
        <div class="modal-header">
          <div class="modal-heading">
            <span class="modal-heading-icon"><svg class="icon" viewBox="0 0 24 24" aria-hidden="true">
                <circle cx="12" cy="12" r="9"></circle>
                <path d="M12 7v5l3 2"></path>
              </svg></span>
            <div>
              <h2 id="equipment-view-title">Equipment availability</h2>
              <p>{{ $this->viewedEquipment->name }} · {{ $this->viewedEquipment->asset_code }}</p>
            </div>
          </div>
          <button class="icon-button modal-close" type="button" wire:click="closeEquipmentView" aria-label="Close dialog">
            <svg class="icon" viewBox="0 0 24 24" aria-hidden="true">
              <path d="M18 6 6 18M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        <div class="modal-body">
          <div class="availability-panel">
            <div><small>Total inventory</small><strong>{{ $this->viewedEquipment->total_quantity }}</strong></div>
            <div><small>Available units</small><strong>{{ $this->viewedEquipment->available_quantity }}</strong></div>
            <div><small>Condition</small><strong>{{ $this->viewedEquipment->condition }}</strong></div>
          </div>
          <div class="table-wrap">
            <table class="data-table responsive-table">
              <thead>
                <tr>
                  <th>Time slot</th>
                  <th>Available units</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                @foreach (['7:30 AM – 10:30 AM', '11:30 AM – 2:00 PM', '2:15 PM – 5:15 PM'] as $timeSlot)
                  <tr wire:key="availability-slot-{{ $loop->index }}">
                    <td data-label="Time slot"><span class="cell-primary">{{ $timeSlot }}</span></td>
                    <td data-label="Available units">
                      <strong class="{{ $this->viewedEquipment->available_quantity > 0 ? 'text-success' : '' }}">
                        {{ $this->viewedEquipment->available_quantity }}
                      </strong>
                    </td>
                    <td data-label="Status">
                      @if ($this->viewedEquipment->condition === 'Good' && $this->viewedEquipment->available_quantity > 0)
                        <span class="status-badge status-available">Available</span>
                      @else
                        <span class="status-badge status-unavailable">Not bookable</span>
                      @endif
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          <div class="form-note" style="margin-top:15px">
            <svg class="icon" viewBox="0 0 24 24" aria-hidden="true">
              <circle cx="12" cy="12" r="9"></circle>
              <path d="M12 11v6M12 7h.01"></path>
            </svg>
            <span>Availability reflects current inventory. Reservations are not yet tracked by time slot.</span>
          </div>
        </div>
        <div class="modal-footer">
          <button class="button button-primary" type="button" wire:click="closeEquipmentView">Done</button>
        </div>
      </div>
    </div>
  @endif

  {{-- Delete equipment modal --}}
  @if ($deletingEquipmentId !== null && $this->equipmentToDelete)
    <div class="modal-backdrop" id="modal-backdrop">
      <div class="modal modal-small" id="modal" role="dialog" aria-modal="true" aria-labelledby="modal-title">
        <div id="modal-content">
          <div class="modal-header">
            <div class="modal-heading"><span class="modal-heading-icon danger"><svg class="icon " viewBox="0 0 24 24"
                  aria-hidden="true">
                  <path d="M3 6h18M8 6V3h8v3M19 6l-1 15H6L5 6M10 11v5M14 11v5"></path>
                </svg></span>
              <div>
                <h2 id="modal-title">Remove equipment from inventory?</h2>
                <p>Please review this action before continuing.</p>
              </div>
            </div><button class="icon-button modal-close" type="button" wire:click="closeDeleteModal" aria-label="Close dialog"><svg
                class="icon " viewBox="0 0 24 24" aria-hidden="true">
                <path d="M18 6 6 18M6 6l12 12"></path>
              </svg></button>
          </div>
          <div class="modal-body">
            <div class="confirm-message">Remove <strong>{{ $this->equipmentToDelete->name }}</strong> and its {{ $this->equipmentToDelete->total_quantity }} unit(s)? Historical
              booking and usage records will retain the equipment name, but students will no longer see it in the
              catalogue.</div>
          </div>
          <div class="modal-footer"><button class="button button-outline" type="button" wire:click="closeDeleteModal">Go
              back</button><button class="button button-danger" type="button" wire:click="deleteEquipment" id="confirm-action"><svg class="icon "
                viewBox="0 0 24 24" aria-hidden="true">
                <path d="M18 6 6 18M6 6l12 12"></path>
              </svg> Remove equipment</button></div>
        </div>
      </div>
    </div>
  @endif
</div>