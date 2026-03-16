@extends('layouts.app')

@section('content')
    <section class="max-w-md mx-auto">
        <div class="ssb-card space-y-4">
            <h1 class="text-2xl ssb-title">Log in</h1>
            <p class="ssb-muted">Ielogojies kā sacensību rīkotājs.</p>

            <form method="POST" action="{{ route('login.attempt') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="identity" class="block text-sm ssb-muted mb-1">Username vai e-pasts</label>
                    <input
                        id="identity"
                        name="identity"
                        type="text"
                        value="{{ old('identity') }}"
                        required
                        class="w-full rounded-lg border border-zinc-700 bg-zinc-900 px-3 py-2 text-zinc-100 focus:outline-none focus:ring-2 focus:ring-orange-400"
                    >
                    @error('identity')
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
                        class="w-full rounded-lg border border-zinc-700 bg-zinc-900 px-3 py-2 text-zinc-100 focus:outline-none focus:ring-2 focus:ring-orange-400"
                    >
                </div>

                <button type="submit" class="ssb-button-primary w-full">Ielogoties</button>
            </form>
        </div>
    </section>
@endsection
