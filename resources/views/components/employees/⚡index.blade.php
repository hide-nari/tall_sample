<?php

use App\Models\Employee;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;
    use WithFileUploads;

    public $sortBy = 'id';
    public $sortDirection = 'asc';
    public $search = '';
    public $trashViewFlg = false;

    #[Validate('required')]
    public $image;

    #[Computed]
    public function employees(): LengthAwarePaginator
    {
        return Employee::withTrashed($this->trashViewFlg)
            ->when($this->search, function (Builder $query) {
                $query->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%')
                    ->withTrashed($this->trashViewFlg);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(10);
    }

    public function sort($column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function delete($employerId): void
    {
        Employee::findOrFail($employerId)->delete();
        Flux::toast(
            text: 'delete employer record.',
            variant: 'danger'
        );
    }

    public function imageUpload(Employee $employer): void
    {
        $this->validate();
        $employer->update([
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

    <div class="flex mb-5 mt-5">
        <flux:input wire:model.live="search" label="Search:"/>
        <flux:spacer/>
        <flux:modal.trigger name="filter">
            <flux:button icon="funnel" icon:variant="outline" class="mr-4 mt-7"/>
        </flux:modal.trigger>
        <flux:button icon="plus" href="{{ route('employees.show') }}" class="mr-4 mt-7"/>
    </div>

    <flux:modal name="filter" class="w-96">
        <div class="space-y-4">
            <flux:checkbox.group label="Filter" class="mt-2">
                <flux:checkbox label="Delete Data with Table" wire:model="trashViewFlg"/>
            </flux:checkbox.group>
            <flux:button wire:click="$refresh">Apply</flux:button>
        </div>
    </flux:modal>

    <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
        {{--        <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20"/>--}}
        <flux:table>
            <flux:table.columns>
                <flux:table.column sortable
                                   align="center"
                                   :sorted="$sortBy === 'id'"
                                   :direction="$sortDirection"
                                   wire:click="sort('id')"
                                   class="w-24">
                    ID
                </flux:table.column>
                <flux:table.column sortable
                                   :sorted="$sortBy === 'name'"
                                   :direction="$sortDirection"
                                   wire:click="sort('name')">
                    Name
                </flux:table.column>
                <flux:table.column sortable
                                   :sorted="$sortBy === 'email'"
                                   :direction="$sortDirection"
                                   wire:click="sort('email')">
                    Email
                </flux:table.column>
                <flux:table.column sortable
                                   :sorted="$sortBy === 'department_id'"
                                   :direction="$sortDirection"
                                   wire:click="sort('department_id')">
                    Department
                </flux:table.column>
                <flux:table.column>
                    Image
                </flux:table.column>

                <flux:table.column sortable
                                   :sorted="$sortBy === 'created_at'"
                                   :direction="$sortDirection"
                                   wire:click="sort('created_at')"
                                   class="w-48">
                    CreateDate
                </flux:table.column>
                <flux:table.column sortable
                                   :sorted="$sortBy === 'updated_at'"
                                   :direction="$sortDirection"
                                   wire:click="sort('updated_at')"
                                   class="w-48">
                    UpdateDate
                </flux:table.column>
                <flux:table.column align="center" class="w-24">Edit</flux:table.column>
                <flux:table.column align="center" class="w-24">Delete</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @foreach($this->employees as $employee)
                    <flux:modal :name="'image_upload_'.$employee->id" class="w-96">
                        <div class="space-y-4">
                            @if ($employee->image)
                                <img src="{{ Storage::url($employee->image) }}" width="200px">
                                <flux:separator/>
                            @endif
                            @if ($image)
                                <img src="{{ $image->temporaryUrl() }}" width="200px">
                            @endif
                            <flux:input type="file" wire:model="image" label="Image:"/>
                            <flux:button wire:click="imageUpload({{ $employee }})">Upload</flux:button>
                        </div>
                    </flux:modal>

                    <flux:table.row :key="$employee->id" class="hover:bg-zinc-100 dark:hover:bg-zinc-700">
                        <flux:table.cell align="center">{{ $employee->id }}</flux:table.cell>
                        <flux:table.cell>
                            {{ $employee->name }}
                        </flux:table.cell>
                        <flux:table.cell>
                            {{ $employee->email }}
                        </flux:table.cell>
                        <flux:table.cell>
                            {{ $employee->department->name ?? '' }}
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex">
                                @if($employee->image)
                                    <flux:avatar src="{{ Storage::url($employee->image) }}"/>
                                @else
                                    <flux:avatar icon="user" color="auto" color:seed="{{ $employee->id }}"/>
                                @endif
                                @unless($employee->deleted_at)
                                    <flux:modal.trigger :name="'image_upload_'.$employee->id">
                                        <flux:button icon="cloud-arrow-up" variant="ghost" icon:variant="outline"/>
                                    </flux:modal.trigger>
                                @endif
                            </div>

                        </flux:table.cell>
                        <flux:table.cell>{{ $employee->created_at }}</flux:table.cell>
                        <flux:table.cell>{{ $employee->updated_at }}</flux:table.cell>
                        <flux:table.cell align="center">
                            @unless($employee->deleted_at)
                                <flux:button
                                    href="{{ route('employees.show',$employee) }}"
                                    variant="subtle"
                                    size="xs"
                                >
                                    <flux:icon.pencil/>
                                </flux:button>
                            @else
                                <flux:button
                                    as="div"
                                    variant="subtle"
                                    size="xs"
                                >
                                    <flux:icon.pencil-off variant="mini"/>
                                </flux:button>
                            @endunless
                        </flux:table.cell>
                        <flux:table.cell align="center">
                            @unless($employee->deleted_at)
                                <flux:button
                                    wire:confirm="Delete OK?"
                                    wire:click="delete({{ $employee->id }})"
                                    variant="subtle"
                                    size="xs"
                                >
                                    <flux:icon.trash/>
                                </flux:button>
                            @else
                                <flux:button
                                    as="div"
                                    variant="subtle"
                                    size="xs"
                                >
                                    <flux:icon.save-off/>
                                </flux:button>
                            @endunless
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </div>
    <flux:pagination :paginator="$this->employees"/>
</div>
