@extends('layouts.app')

@section('content')
    <section class="max-w-3xl mx-auto">
        <h1 class="text-3xl ssb-title mb-2">Mans profils</h1>
        <p class="ssb-muted mb-6">Šeit vari nomainīt savu lietotājvārdu.</p>

        @if (session('status'))
            <div class="ssb-card mb-4">
                <p class="ssb-text">{{ session('status') }}</p>
            </div>
        @endif

        <form method="POST" action="{{ route('profile.update') }}" class="ssb-card space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-sm ssb-muted mb-1">Lietotājvārds</label>
                <input
                    id="name"
                    name="name"
                    type="text"
                    value="{{ old('name', auth()->user()->name) }}"
                    required
                    class="ssb-form-control"
                >
                @error('name')
                    <p class="text-sm text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm ssb-muted mb-1">E-pasts</label>
                <input type="text" value="{{ auth()->user()->email }}" disabled class="ssb-form-control opacity-80 cursor-not-allowed">
            </div>

            <div class="flex flex-wrap gap-3">
                <button type="submit" class="ssb-button-primary">Saglabāt</button>
                @if (auth()->user()->is_admin)
                    <a href="{{ route('admin.profile') }}" class="ssb-button-secondary">Atvērt admin profilu</a>
                @endif
            </div>
        </form>
    </section>
@endsection
