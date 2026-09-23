<?php

use App\Models\Department;
use Livewire\Attributes\Validate;
use Livewire\Component;

new class extends Component {
    public Department $department;

    public ?int $id = null;
    #[Validate('required')]
    public ?string $name;
    public ?string $method = 'update';

    public function mount(Department $department): void
    {
        $department->id ? $this->id = $department->id : $this->method = 'add';
        $this->name = $department->name;
    }

    public function add(): void
    {
        $this->validate();
        $department = Department::create([
            'name' => $this->pull('name'),
        ]);
        Flux::toast(
            text: "created $department->name record.",
            variant: 'success'
        );
    }

    public function update(): void
    {
        $this->validate();
        Department::findOrFail($this->id)->update([
            'name' => $this->name,
        ]);
        Flux::toast(
            text: "updated department record.",
            variant: 'warning'
        );
    }
};
?>

<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <flux:card class="lg:w-1/3">
        <flux:input wire:model="name" label="Name:"></flux:input>
        <br>
        <div wire:dirty="name">
            <flux:button
                wire:click="{{$method}}"
                variant="primary"
                class="mr-2"
            >{{ Str::ucfirst($method) }}
            </flux:button>
        </div>
        <flux:button href="{{ route('departments.index') }}" variant="filled">
            Back
        </flux:button>
    </flux:card>
</div>
