<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @lang('crud.permissions.edit_title')
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-partials.card>
                <x-slot name="title">
                    <a href="{{ route('permissions.index') }}" class="mr-4"
                        ><i class="mr-1 fa-duotone fa-arrow-left"></i
                    ></a>
                </x-slot>

                <x-form
                    method="PUT"
                    action="{{ route('permissions.update', $permission) }}"
                    class="mt-4"
                >
                    @include('app.permissions.form-inputs')

                    <div class="mt-10">
                        <a
                            href="{{ route('permissions.index') }}"
                            class="button"
                        >
                            <i
                                class="fa-duotone fa-arrow-turn-down-left mr-1"
                            ></i>
                            @lang('crud.common.back')
                        </a>

                        <a
                            href="{{ route('permissions.create') }}"
                            class="button"
                        >
                            <i class="mr-1 fa-duotone fa-circle-plus text-primary"></i>
                            @lang('crud.common.create')
                        </a>

                        <button
                            type="submit"
                            class="button button-primary float-right"
                        >
                            <i class="fa-duotone fa-floppy-disks mr-1"></i>
                            @lang('crud.common.update')
                        </button>
                    </div>
                </x-form>
            </x-partials.card>
        </div>
    </div>
</x-app-layout>
