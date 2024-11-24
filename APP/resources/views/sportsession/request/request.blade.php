<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Sport Session Request') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="container mt-5">
                        <h2>Sport Session Request Form</h2>
                        <form action="/session-request" method="POST">
                            @csrf

                            <!-- Error Messages -->
                            @if ($errors->any())
                                <div class="mb-4">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li class="text-white-500">{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="form-group">
                                <label for="goal">What is your goal?</label>
                                <input type="text" class="form-control" id="goal" name="goal" required>
                            </div>
                            <div class="form-group">
                                <label for="level">What is your level?</label>
                                <select class="form-control" id="level" name="level" required>
                                    <option value="beginner">Beginner</option>
                                    <option value="intermediate">Intermediate</option>
                                    <option value="advanced">Advanced</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="machines">Which machines do you have access to?</label>
                                <input type="text" class="form-control" id="machines" name="machines" required>
                            </div>

                            <div class="form-group">
                                <label for="machines"></label>
                                <label for="duration">Programme sur combien de semaine?</label>
                                <input type="range" class="form-control" id="duration" name="duration" min="1" max="8" required>
                            </div>

                            <button type="submit" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>