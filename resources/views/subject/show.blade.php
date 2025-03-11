<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Subject Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">
                                {{ __('Subject Information') }}
                            </h2>

                            <p class="mt-1 text-sm text-gray-600">
                                {{ __("View information about this subject.") }}
                            </p>
                        </header>

                        <div class="mt-6 space-y-6">
                            <div>
                                <x-input-label for="name" :value="__('Name')" />
                                <p class="mt-1 font-medium text-gray-900">{{ $subject->latestDetail->name }}</p>
                            </div>

                            <div>
                                <x-input-label for="description" :value="__('Description')" />
                                <p class="mt-1 text-gray-900">{{ $subject->latestDetail->description ?? __('No description provided.') }}</p>
                            </div>

                            <div class="flex items-center gap-4">
                                <a href="{{ route('organization.subject.edit', ['organization' => $organization->id, 'subject' => $subject->id]) }}">
                                    <x-primary-button>{{ __('Edit') }}</x-primary-button>
                                </a>
                                <a href="{{ route('organization.show', ['organizationId' => $organization->id]) }}">
                                    <x-secondary-button>{{ __('Back to Organization') }}</x-secondary-button>
                                </a>
                                
                                @if (session('status') === 'subject-updated')
                                    <p
                                        x-data="{ show: true }"
                                        x-show="show"
                                        x-transition
                                        x-init="setTimeout(() => show = false, 2000)"
                                        class="text-sm text-gray-600"
                                    >{{ __('Saved.') }}</p>
                                @endif
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>