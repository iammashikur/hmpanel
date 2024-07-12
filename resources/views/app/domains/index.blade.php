<x-app-layout>
    <x-slot name="header">
        @lang('crud.domains.index_title') </x-slot>


    <div class="container full-container py-5 flex flex-col gap-6">
        <x-partials.card>
            <div class="mb-5 mt-4">
                <div class="flex flex-wrap justify-between pt-5 px-4">
                    <div class="md:w-1/2">
                        <form>
                            <div class="flex items-center w-full">
                                <x-inputs.text name="search" value="{{ $search ?? '' }}"
                                    placeholder="{{ __('crud.common.search') }}" autocomplete="off"></x-inputs.text>

                                <div class="ml-1">
                                    <button type="submit" class="button button-primary">
                                        <i class="fa-duotone fa-magnifying-glass"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="md:w-1/2 text-right">
                        @can('create', App\Models\Domain::class)
                            <a href="{{ route('domains.create') }}" class="button button-primary">
                                <i class="mr-1 fa-duotone fa-circle-plus"></i>
                                @lang('crud.common.create')
                            </a>
                        @endcan
                    </div>
                </div>
            </div>

            <div class="block w-full overflow-auto scrolling-touch">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-3 text-left">
                                @lang('crud.domains.inputs.name')
                            </th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600">
                        @forelse($domains as $domain)
                            <tr
                                class="hover:bg-gray-50 {{ $loop->last ? '' : 'border-b' }} {{ $loop->index % 2 !== 0 ? 'bg-gray-50' : '' }}">
                                <td class="px-4 py-3 text-left">
                                    {{ $domain->name ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-center" style="width: 134px;">
                                    <div role="group" aria-label="Row Actions"
                                        class="
                                            relative
                                            inline-flex
                                            align-middle
                                        ">
                                        @can('update', $domain)
                                            <a href="{{ route('domains.edit', $domain) }}" class="mr-1">
                                                <button type="button" class="button">
                                                    <i class="fa-duotone fa-pen-to-square"></i>
                                                </button>
                                            </a>
                                            @endcan @can('view', $domain)
                                            <a href="{{ route('domains.show', $domain) }}" class="mr-1">
                                                <button type="button" class="button">
                                                    <i class="fa-duotone fa-eye"></i>
                                                </button>
                                            </a>
                                            @endcan @can('delete', $domain)
                                            <form action="{{ route('domains.destroy', $domain) }}" method="POST"
                                                onsubmit="return confirm('{{ __('crud.common.are_you_sure') }}')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="button">
                                                    <i class="fa-duotone fa-trash-can"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="p-2 text-center">
                                    @lang('crud.common.no_items_found')
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2" class="p-2 text-center">
                                <div class="mt-10 px-4">
                                    {!! $domains->render() !!}
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </x-partials.card>
    </div>

</x-app-layout>
