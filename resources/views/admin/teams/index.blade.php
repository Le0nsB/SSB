@extends('layouts.app')

@section('content')
    <section class="max-w-5xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl ssb-title">Admin — Komandas</h1>
            <a href="{{ route('admin.teams.create') }}" class="ssb-button-primary">Pievienot komandu</a>
        </div>

        @forelse ($teams as $team)
            <article class="ssb-card mb-4 flex items-center justify-between gap-3">
                <h2 class="text-lg font-semibold text-zinc-100">{{ $team->name }}</h2>

                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.teams.edit', $team) }}" class="ssb-button-secondary">Rediģēt</a>
                    <form method="POST" action="{{ route('admin.teams.destroy', $team) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="ssb-button-secondary">Dzēst</button>
                    </form>
                </div>
            </article>
        @empty
            <p class="ssb-muted">Komandas vēl nav pievienotas.</p>
        @endforelse
    </section>
@endsection
