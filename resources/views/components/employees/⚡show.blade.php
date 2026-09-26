<?php

use App\Models\Department;
use App\Models\Employee;
use Illuminate\Support\Collection;
use Livewire\Attributes\Validate;
use Livewire\Component;

new class extends Component {
    public Employee $employee;
    public Collection $departments;

    public ?int $id = null;

    public $method = 'update';

    #[Validate('required')]
    public ?string $name;

    #[Validate('required')]
    public ?string $email;

    #[Validate('required|gte:0')]
    public ?int $department_id;


    public function mount(Employee $employee): void
    {
        $employee->id ? $this->id = $employee->id : $this->method = 'add';
        $this->name = $employee->name;
        $this->email = $employee->email;
        $this->department_id = $employee->department_id;
        $this->departments = Department::all();;
    }

    public function add(): void
    {
        $this->validate();
        $employee = Employee::create([
            'name' => $this->pull('name'),
            'email' => $this->pull('email'),
            'department_id' => $this->pull('department_id'),
        ]);
        Flux::toast(
            text: "created $employee->name record.",
            variant: 'success'
        );
    }

    public function update(): void
    {
        $this->validate();
        Employee::findOrFail($this->id)->update([
            'name' => $this->name,
            'email' => $this->email,
            'department_id' => $this->department_id,
        ]);
        Flux::toast(
            text: "updated record.",
            variant: 'success'
        );
    }
};
?>

<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <flux:card class="lg:w-1/3 space-y-4">
        <flux:input wire:model="name" label="Name:"/>
        <flux:input wire:model="email" label="Email:"/>
        <flux:select wire:model="department_id" label="Department:">
            <flux:select.option value="0">----</flux:select.option>
            @foreach($departments as $department)
                <flux:select.option value="{{ $department->id }}">{{ $department->name }}</flux:select.option>
            @endforeach
        </flux:select>
        <div class="mt-6">
            <div wire:dirty="name">
                <flux:button
                    wire:click="{{$method}}"
                    variant="primary"
                    class="mr-2"
                >{{ Str::ucfirst($method) }}
                </flux:button>
            </div>
            <flux:button href="{{ route('employees.index') }}" variant="filled">Back</flux:button>
        </div>
    </flux:card>
</div>
