@extends('layouts.app')

@section('content')
    <h1 class="text-2xl md:text-3xl mb-2 ssb-title">Jaunumi</h1>
    <p class="ssb-muted mb-6">Aktualitātes, rezultāti un viss, kas notiek Sunny Streetball kopienā.</p>

    @forelse ($newsPosts as $news)
        <article class="ssb-card-line pb-4 mb-4">
            <h2 class="font-semibold text-zinc-100">{{ $news->title }}</h2>
            @if ($news->published_at)
                <p class="text-sm ssb-muted mt-1">{{ $news->published_at->format('d.m.Y H:i') }}</p>
            @endif

            @if ($news->excerpt)
                <p class="ssb-text mt-2">{{ $news->excerpt }}</p>
            @endif
            
            @php
                $assignedMedia = $newsMediaByTitle[$news->title] ?? ($mediaItems[$loop->index] ?? null);
            @endphp

            @if (!empty($news->image_path))
                <img src="{{ asset($news->image_path) }}" alt="{{ $news->title }}" class="w-full max-w-lg rounded-lg mt-3 object-cover">
            @elseif ($assignedMedia)
                @if ($assignedMedia['kind'] === 'video')
                    <video class="w-full max-w-lg rounded-lg mt-3" controls preload="metadata">
                        <source src="{{ $assignedMedia['url'] }}" type="{{ $assignedMedia['type'] }}">
                        Tava pārlūkprogramma neatbalsta video atskaņošanu.
                    </video>
                @else
                    <img src="{{ $assignedMedia['url'] }}" alt="{{ $assignedMedia['name'] }}" class="w-full max-w-lg rounded-lg mt-3 object-cover">
                @endif
            @endif
        </article>
    @empty
        <p class="ssb-muted">Jaunumi vēl nav publicēti.</p>
    @endforelse
@endsection
