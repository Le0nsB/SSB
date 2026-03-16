@extends('layouts.app')

@section('content')
    <section class="max-w-5xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl ssb-title">Admin — Sacensības</h1>
            <a href="{{ route('admin.competitions.create') }}" class="ssb-button-primary">Pievienot sacensības</a>
        </div>

        @forelse ($competitions as $competition)
            <article class="ssb-card mb-4">
                <h2 class="text-lg font-semibold text-zinc-100">{{ $competition->title }}</h2>
                <p class="ssb-text mt-1">{{ $competition->location }} • {{ $competition->event_date->format('d.m.Y') }}</p>
                <p class="ssb-muted mt-1">Publicēts: {{ $competition->is_published ? 'Jā' : 'Nē' }}</p>

                <div class="mt-3">
                    <a href="{{ route('admin.competitions.edit', $competition) }}" class="ssb-button-secondary">Rediģēt</a>
                </div>
            </article>
        @empty
            <p class="ssb-muted">Sacensības vēl nav pievienotas.</p>
        @endforelse
    </section>
@endsection
