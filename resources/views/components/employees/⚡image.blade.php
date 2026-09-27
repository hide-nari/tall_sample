<?php

use App\Models\Employee;
use Illuminate\Support\Collection;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    public Employee $employee;

    #[Validate('required')]
    public $image;

//    public function mount(Employee $employee): void
//    {
//        $employee->id ? $this->id = $employee->id : $this->method = 'add';
//    }

    public function imageUpload(Employee $employee): void
    {
        $this->validate();
        $employee->update([
            'image' => $this->pull('image')->store('images'),
        ]);
        Flux::toast(
            text: 'Image updated.',
            variant: 'success'
        );
    }

};
?>

<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    {{--    <flux:card class="lg:w-1/3 space-y-4">--}}
    {{--        <flux:input type="file" wire:model="image" label="Image:"/>--}}
    {{--        <flux:button wire:click="imageUpload({{ $employee }})">Upload</flux:button>--}}
    {{--        <flux:input wire:model="name" label="Name:"/>--}}
    {{--        <flux:input wire:model="email" label="Email:"/>--}}
    {{--        <flux:select wire:model="department_id" label="Department:">--}}
    {{--            <flux:select.option value="0">----</flux:select.option>--}}
    {{--            @foreach($departments as $department)--}}
    {{--                <flux:select.option value="{{ $department->id }}">{{ $department->name }}</flux:select.option>--}}
    {{--            @endforeach--}}
    {{--        </flux:select>--}}
    {{--        <div class="mt-6">--}}
    {{--            <div wire:dirty="name">--}}
    {{--                <flux:button--}}
    {{--                    wire:click="{{$method}}"--}}
    {{--                    variant="primary"--}}
    {{--                    class="mr-2"--}}
    {{--                >{{ Str::ucfirst($method) }}--}}
    {{--                </flux:button>--}}
    {{--            </div>--}}
    {{--            <flux:button href="{{ route('employees.index') }}" variant="filled">Back</flux:button>--}}
    {{--        </div>--}}
    {{--        <flux:button href="{{ route('employees.index') }}" variant="filled">Back</flux:button>--}}
    {{--    </flux:card>--}}
    <flux:card class="lg:w-1/3 space-y-4">
        @if ($employee->image)
            <img src="{{ Storage::url($employee->image) }}" width="200px">
            <flux:separator/>
        @endif
{{--        @if ($image)--}}
{{--            <img src="{{ $image->temporaryUrl() }}" width="200px">--}}
{{--        @endif--}}
        <flux:input type="file" wire:model="image" label="Image:"/>
        <flux:button
            wire:click="imageUpload"
            variant="primary"
            class="mr-2"
        >{{ 'imageUpload' }}
        </flux:button>
        <flux:button href="{{ route('employees.index') }}" variant="filled">
            Back
        </flux:button>
    </flux:card>

</div>
