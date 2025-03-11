<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Organization') }}
            </h2>
            <a href="{{ route('organization.subject.create', ['organization' => $organization->id]) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 focus:bg-green-500 active:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                {{ __('Create New Subject') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @if (session('status') === 'organization-updated')
                        <div class="mb-4 font-medium text-sm text-green-600">
                            {{ __('Organization information has been updated.') }}
                        </div>
                    @endif
                    
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">
                                {{ __('Organization Information') }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-600">
                                {{ __("View your organization's information.") }}
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
                                <a href="{{ route('organization.edit', ['organizationId' => $organization->id]) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    {{ __('Edit') }}
                                </a>
                                <a href="{{ route('organization.index') }}" class="text-indigo-600 hover:text-indigo-900">
                                    {{ __('Back to Organizations') }}
                                </a>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
        
        <!-- Subjects List Section -->
        <div class="mt-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">
                                {{ __('Subjects') }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-600">
                                {{ __("Manage your organization's subjects.") }}
                            </p>
                        </header>

                        <div class="mt-6 space-y-6">
                            @if ($organization->subjects->isEmpty())
                                <p class="text-gray-600">{{ __('No subjects found for this organization.') }}</p>
                            @else
                                <div class="space-y-4">
                                    @foreach ($organization->subjects as $subject)
                                        <div class="border rounded-lg p-4 hover:bg-gray-50">
                                            <h3 class="font-semibold text-lg">{{ $subject->latestDetail->name }}</h3>
                                            <p class="text-gray-600 mt-1">{{ $subject->latestDetail->description ?? __('No description') }}</p>
                                            <div class="mt-4 flex space-x-2">
                                                <a href="{{ route('organization.subject.show', ['organization' => $organization->id, 'subject' => $subject->id]) }}" class="text-indigo-600 hover:text-indigo-900">
                                                    {{ __('View') }}
                                                </a>
                                                <span class="text-gray-300">|</span>
                                                <a href="{{ route('organization.subject.edit', ['organization' => $organization->id, 'subject' => $subject->id]) }}" class="text-indigo-600 hover:text-indigo-900">
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