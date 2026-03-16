@extends('layouts.app')

@section('content')
    @php
        $weekdays = [
            1 => 'Pirmdiena',
            2 => 'Otrdiena',
            3 => 'Trešdiena',
            4 => 'Ceturtdiena',
            5 => 'Piektdiena',
            6 => 'Sestdiena',
            7 => 'Svētdiena',
        ];

        $eventDay = $nextCompetition ? ($weekdays[$nextCompetition->event_date->dayOfWeekIso] ?? '') : null;
        $eventTime = data_get($nextCompetition, 'start_time', '11:00');
        $registeredTeams = data_get($nextCompetition, 'team_limit');
        $registrationFee = data_get($nextCompetition, 'entry_fee');
    @endphp

    @if ($latestFinishedCompetition)
        <section class="mb-8 ssb-card">
            <h2 class="text-2xl ssb-title">Pēdējo sacensību rezultāti</h2>
            <p class="ssb-text mt-1">{{ $latestFinishedCompetition->title }} • {{ $latestFinishedCompetition->event_date->format('d.m.Y') }}</p>

            <div class="mt-4 grid gap-6 md:grid-cols-2">
                <div>
                    <h3 class="text-lg ssb-subtitle">Spēles un punkti</h3>
                    @forelse ($latestFinishedCompetition->matches ?? [] as $match)
                        <p class="ssb-text mt-1">{{ $match->homeTeam?->name }} {{ $match->home_score }} : {{ $match->away_score }} {{ $match->awayTeam?->name }}</p>
                    @empty
                        <p class="ssb-muted mt-1">Rezultāti vēl nav pievienoti.</p>
                    @endforelse
                </div>

                <div>
                    <h3 class="text-lg ssb-subtitle">Gala vietas</h3>
                    @forelse ($latestFinishedCompetition->teams ?? [] as $team)
                        <p class="ssb-text mt-1">{{ $team->final_position }}. vieta — {{ $team->name }}</p>
                    @empty
                        <p class="ssb-muted mt-1">Gala vietas vēl nav pievienotas.</p>
                    @endforelse
                </div>
            </div>
        </section>
    @endif

    <section class="grid gap-8 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <p class="flex items-center gap-2 text-sm ssb-muted mb-3">
                <span class="ssb-dot"></span>
                SUNNY STREETBALL AKTUALITĀTES
            </p>
            <h1 class="text-3xl md:text-4xl ssb-title mb-2">Aktuālie jaunumi</h1>
            <div class="ssb-fade-divider mb-4"></div>
            <p class="ssb-text mb-6">Uzzini visu svarīgāko par turnīriem, grafiku un jaunākajiem paziņojumiem.</p>

            @forelse ($latestNews as $news)
                <article class="ssb-card ssb-news-article mb-4">
                    <h2 class="text-lg font-semibold text-zinc-100">{{ $news->title }}</h2>
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
                <p class="ssb-muted">Pašlaik nav publicētu jaunumu.</p>
            @endforelse
        </div>

        <aside class="lg:col-span-1">
            <div class="ssb-animated-border">
                <div class="ssb-card space-y-4">
                    <h2 class="text-lg font-bold ssb-subtitle">Nākamās sacensības</h2>

                    @if ($nextCompetition)
                        <div>
                            <p class="text-sm ssb-muted">{{ $eventDay }}</p>
                            <h3 class="text-xl font-semibold text-zinc-100">{{ $nextCompetition->title }}</h3>
                        </div>

                        <dl class="space-y-2 text-sm">
                            <div class="flex justify-between gap-3">
                                <dt class="ssb-muted">Datums</dt>
                                <dd class="ssb-text">{{ $nextCompetition->event_date->format('d.m.Y') }}</dd>
                            </div>
                            <div class="flex justify-between gap-3">
                                <dt class="ssb-muted">Laiks</dt>
                                <dd class="ssb-text">{{ $eventTime }}</dd>
                            </div>
                            <div class="flex justify-between gap-3">
                                <dt class="ssb-muted">Vieta</dt>
                                <dd class="ssb-text text-right">{{ $nextCompetition->location }}</dd>
                            </div>
                            <div class="flex justify-between gap-3">
                                <dt class="ssb-muted">Pieteiktās komandas</dt>
                                <dd class="ssb-text">{{ $registeredTeams ? $registeredTeams : 'Nav norādīts' }}</dd>
                            </div>
                            <div class="flex justify-between gap-3">
                                <dt class="ssb-muted">Dalības maksa</dt>
                                <dd class="ssb-accent font-semibold">{{ $registrationFee !== null ? number_format((float) $registrationFee, 2).' €' : 'Nav norādīta' }}</dd>
                            </div>
                        </dl>

                        @if ($nextCompetition->registration_deadline)
                            <p class="text-sm ssb-accent">Pieteikšanās līdz {{ $nextCompetition->registration_deadline->format('d.m.Y') }}</p>
                        @endif

                        <button type="button" class="ssb-button-primary w-full">Pieteikt Komandu</button>
                    @else
                        <p class="ssb-muted">Pašlaik nav publicētu nākamo sacensību.</p>
                    @endif
                </div>
            </div>
        </aside>
    </section>
@endsection
