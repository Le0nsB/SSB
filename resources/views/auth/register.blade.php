@extends('layouts.app')

@section('content')
    <section class="max-w-md mx-auto">
        <div class="ssb-card space-y-4">
            <h1 class="text-2xl ssb-title">Reģistrācija</h1>
            <p class="ssb-muted">Izveido kontu, lai varētu pieteikt komandu.</p>

            <form method="POST" action="{{ route('register.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="name" class="block text-sm ssb-muted mb-1">Lietotājvārds</label>
                    <input
                        id="name"
                        name="name"
                        type="text"
                        value="{{ old('name') }}"
                        required
                        class="ssb-form-control focus:outline-none focus:ring-2 focus:ring-orange-400"
                    >
                    @error('name')
                        <p class="text-sm text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm ssb-muted mb-1">E-pasts</label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        required
                        class="ssb-form-control focus:outline-none focus:ring-2 focus:ring-orange-400"
                    >
                    @error('email')
                        <p class="text-sm text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm ssb-muted mb-1">Parole</label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        required
                        class="ssb-form-control focus:outline-none focus:ring-2 focus:ring-orange-400"
                    >
                    @error('password')
                        <p class="text-sm text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm ssb-muted mb-1">Apstiprini paroli</label>
                    <input
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        required
                        class="ssb-form-control focus:outline-none focus:ring-2 focus:ring-orange-400"
                    >
                </div>

                <button type="submit" class="ssb-button-primary w-full">Reģistrēties</button>
            </form>

            <p class="text-sm ssb-muted">
                Jau ir konts?
                <a href="{{ route('login') }}" class="ssb-accent font-semibold">Ielogoties</a>
            </p>
        </div>
    </section>
@endsection
