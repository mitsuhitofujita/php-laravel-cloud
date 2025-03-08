<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Observer') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @if (session('status') === 'observer-updated')
                        <div class="mb-4 font-medium text-sm text-green-600">
                            {{ __('Observer information has been updated.') }}
                        </div>
                    @endif
                    
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">
                                {{ __('Observer Information') }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-600">
                                {{ __("View your observer's information.") }}
                            </p>
                        </header>

                        <div class="mt-6 space-y-6">
                            <div>
                                <x-input-label for="name" :value="__('Name')" />
                                <p class="mt-1 font-semibold">{{ $detail->name }}</p>
                            </div>

                            <div>
                                <x-input-label for="description" :value="__('Description')" />
                                <p class="mt-1">{{ $detail->description }}</p>
                            </div>

                            <div class="flex items-center gap-4">
                                <a href="{{ route('observer.edit') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    {{ __('Edit') }}
                                </a>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>