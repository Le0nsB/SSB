@extends('layouts.app')

@section('content')
    <section class="max-w-5xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl ssb-title">Admin — Jaunumi</h1>
            <a href="{{ route('admin.news.create') }}" class="ssb-button-primary">Pievienot jaunumu</a>
        </div>

        @forelse ($newsPosts as $newsPost)
            <article class="ssb-card mb-4">
                <h2 class="text-lg font-semibold text-zinc-100">{{ $newsPost->title }}</h2>
                <p class="ssb-text mt-1">{{ $newsPost->excerpt }}</p>
                <p class="ssb-muted mt-1">Publicēts: {{ $newsPost->is_published ? 'Jā' : 'Nē' }}</p>

                <div class="mt-3">
                    <a href="{{ route('admin.news.edit', $newsPost) }}" class="ssb-button-secondary">Rediģēt</a>
                </div>
            </article>
        @empty
            <p class="ssb-muted">Jaunumi vēl nav pievienoti.</p>
        @endforelse
    </section>
@endsection
