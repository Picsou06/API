<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Size -->
        <div>
            <x-input-label for="size" :value="__('Size (cm)')" />
            <x-text-input id="size" class="block mt-1 w-full" type="number" name="size" :value="old('size')" required autofocus />
            <x-input-error :messages="$errors->get('size')" class="mt-2" />
        </div>

        <!-- Weight -->
        <div class="mt-4">
            <x-input-label for="weight" :value="__('Weight (kg)')" />
            <x-text-input id="weight" class="block mt-1 w-full" type="number" name="weight" :value="old('weight')" required />
            <x-input-error :messages="$errors->get('weight')" class="mt-2" />
        </div>

        <!-- Sex -->
        <div class="mt-4">
            <x-input-label for="sex" :value="__('Sex')" />
            <select id="sex" name="sex" class="block mt-1 w-full" required>
                <option value="male">{{ __('Male') }}</option>
                <option value="female">{{ __('Female') }}</option>
                <option value="other">{{ __('Other') }}</option>
            </select>
            <x-input-error :messages="$errors->get('sex')" class="mt-2" />
        </div>

        <!-- Sessions per week -->
        <div class="mt-4">
            <x-input-label for="sessions_per_week" :value="__('Sessions per week')" />
            <x-text-input id="sessions_per_week" class="block mt-1 w-full" type="number" name="sessions_per_week" :value="old('sessions_per_week')" required />
            <x-input-error :messages="$errors->get('sessions_per_week')" class="mt-2" />
        </div>

        <!-- Session duration -->
        <div class="mt-4">
            <x-input-label for="session_duration" :value="__('Session duration (minutes)')" />
            <x-text-input id="session_duration" class="block mt-1 w-full" type="number" name="session_duration" :value="old('session_duration')" required />
            <x-input-error :messages="$errors->get('session_duration')" class="mt-2" />
        </div>

        <!-- Max weight -->
        <div class="mt-4">
            <x-input-label for="max_weight" :value="__('Max weight (kg)')" />
            <x-text-input id="max_weight" class="block mt-1 w-full" type="number" name="max_weight" :value="old('max_weight')" required />
            <x-input-error :messages="$errors->get('max_weight')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">

            <x-primary-button class="ms-4">
                {{ __('Save') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
