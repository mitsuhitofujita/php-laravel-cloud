<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Organizations') }}
            </h2>
            <a href="{{ route('organization.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                {{ __('Create New Organization') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status') === 'organization-created')
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                    <p>{{ __('Organization has been created successfully.') }}</p>
                </div>
            @endif

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">
                                {{ __('Your Organizations') }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-600">
                                {{ __("Manage your organizations.") }}
                            </p>
                        </header>

                        <div class="mt-6">
                            @if ($organizations->isEmpty())
                                <p class="text-gray-600">{{ __('You have no organizations yet.') }}</p>
                            @else
                                <div class="space-y-4">
                                    @foreach ($organizations as $organization)
                                        <div class="border rounded-lg p-4 hover:bg-gray-50">
                                            <h3 class="font-semibold text-lg">{{ $organization->latestDetail->name }}</h3>
                                            <p class="text-gray-600 mt-1">{{ $organization->latestDetail->description ?? __('No description') }}</p>
                                            <div class="mt-4 flex space-x-2">
                                                <a href="{{ route('organization.show', ['organizationId' => $organization->id]) }}" class="text-indigo-600 hover:text-indigo-900">
                                                    {{ __('View') }}
                                                </a>
                                                <span class="text-gray-300">|</span>
                                                <a href="{{ route('organization.edit', ['organizationId' => $organization->id]) }}" class="text-indigo-600 hover:text-indigo-900">
                                                    {{ __('Edit') }}
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>