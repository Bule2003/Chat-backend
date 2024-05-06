<x-layout>
    <x-slot:heading>
        Log In
    </x-slot:heading>

    <form method="POST" action="/login"> {{--??--}}
        @csrf

        <div>
            <x-form-field>
                <x-form-label for="email">Email</x-form-label> {{--TODO: add first and last name to User schema--}}

                <div class="mt-2">
                    <x-form-input name="email" id="email" type="email" required/>

                    <x-form-error name="email"/>

                </div>
            </x-form-field>

            <x-form-field>
                <x-form-label for="password">Password</x-form-label>

                <div class="mt-2">
                    <x-form-input name="password" id="password" type="password"  required/>

                    <x-form-error name="password"/>

                </div>
            </x-form-field>

        </div>

        <div class="mt-6 flex items-center gap-x-6">
            <x-form-button>Log In</x-form-button>
        </div>

    </form>
</x-layout>
