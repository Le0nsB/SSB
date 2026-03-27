@extends('layouts.app')

@section('content')
    <section class="max-w-3xl mx-auto">
        <h1 class="text-3xl ssb-title mb-6">Rediģēt sacensības</h1>

        <form method="POST" action="{{ route('admin.competitions.update', $competition) }}" class="ssb-card space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm ssb-muted mb-1">Nosaukums</label>
                <input name="title" value="{{ old('title', $competition->title) }}" required class="w-full rounded-lg border border-zinc-700 bg-zinc-900 px-3 py-2 text-zinc-100">
            </div>

            <div>
                <label class="block text-sm ssb-muted mb-1">Vieta</label>
                <input name="location" value="{{ old('location', $competition->location) }}" required class="w-full rounded-lg border border-zinc-700 bg-zinc-900 px-3 py-2 text-zinc-100">
            </div>

            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm ssb-muted mb-1">Datums</label>
                    <input type="date" name="event_date" value="{{ old('event_date', optional($competition->event_date)->format('Y-m-d')) }}" required class="w-full rounded-lg border border-zinc-700 bg-zinc-900 px-3 py-2 text-zinc-100">
                </div>
                <div>
                    <label class="block text-sm ssb-muted mb-1">Pieteikšanās termiņš</label>
                    <input type="date" name="registration_deadline" value="{{ old('registration_deadline', optional($competition->registration_deadline)->format('Y-m-d')) }}" class="w-full rounded-lg border border-zinc-700 bg-zinc-900 px-3 py-2 text-zinc-100">
                </div>
            </div>

            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm ssb-muted mb-1">Komandu skaits</label>
                    <input type="number" min="1" name="team_limit" value="{{ old('team_limit', $competition->team_limit) }}" class="w-full rounded-lg border border-zinc-700 bg-zinc-900 px-3 py-2 text-zinc-100">
                </div>
                <div>
                    <label class="block text-sm ssb-muted mb-1">Dalības maksa (€)</label>
                    <input type="number" min="0" step="0.01" name="entry_fee" value="{{ old('entry_fee', $competition->entry_fee) }}" class="w-full rounded-lg border border-zinc-700 bg-zinc-900 px-3 py-2 text-zinc-100">
                </div>
            </div>

            <div>
                <label class="block text-sm ssb-muted mb-1">Apraksts</label>
                <textarea name="description" rows="4" class="w-full rounded-lg border border-zinc-700 bg-zinc-900 px-3 py-2 text-zinc-100">{{ old('description', $competition->description) }}</textarea>
            </div>

            <label class="flex items-center gap-2 ssb-text">
                <input type="checkbox" name="is_published" value="1" {{ old('is_published', $competition->is_published) ? 'checked' : '' }}>
                Publicēts
            </label>

            <div class="flex flex-wrap items-center gap-3">
                <button type="submit" class="ssb-button-primary">Saglabāt izmaiņas</button>
            </div>
        </form>

        <form method="POST" action="{{ route('admin.competitions.destroy', $competition) }}" class="mt-3" onsubmit="return confirm('Vai tiešām dzēst šo sacensību?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="ssb-button-secondary">Dzēst sacensības</button>
        </form>

        <section class="ssb-card mt-6">
            <h2 class="text-2xl ssb-title mb-4">Komandas</h2>
            <p class="ssb-muted mb-3">
                Pievienotas komandas: {{ $competition->teams->count() }}
                @if ($competition->team_limit)
                    / {{ $competition->team_limit }}
                @endif
            </p>

            @if ($errors->has('name') || $errors->has('team'))
                <div class="mb-3 rounded-lg border border-red-500/60 bg-red-500/10 px-3 py-2 text-sm text-red-200">
                    {{ $errors->first('name') ?: $errors->first('team') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.competitions.teams.store', $competition) }}" class="flex flex-col sm:flex-row gap-3 mb-4">
                @csrf
                <select name="saved_team_id" class="w-full rounded-lg border border-zinc-700 bg-zinc-900 px-3 py-2 text-zinc-100">
                    <option value="">Izvēlies saglabātu komandu</option>
                    @foreach ($savedTeams as $savedTeam)
                        <option value="{{ $savedTeam->id }}">{{ $savedTeam->name }}</option>
                    @endforeach
                </select>
                <input name="name" value="{{ old('name') }}" placeholder="Vai ievadi jaunu komandas nosaukumu" class="w-full rounded-lg border border-zinc-700 bg-zinc-900 px-3 py-2 text-zinc-100">
                <button type="submit" class="ssb-button-primary">Pievienot komandu</button>
            </form>

            @forelse ($competition->teams as $team)
                <p class="ssb-text py-1">• {{ $team->name }}</p>
            @empty
                <p class="ssb-muted">Šai sacensībai vēl nav pievienota neviena komanda.</p>
            @endforelse
        </section>

        <section class="ssb-card mt-6">
            <h2 class="text-2xl ssb-title mb-4">Spēļu rezultāti</h2>
            <p class="ssb-muted mb-3">Spēles posms tiek noteikts automātiski pēc komandu skaita un jau pievienoto spēļu secības.</p>

            @if ($nextMatchStage)
                <p class="ssb-text mb-3">
                    Šobrīd tiek pievienota spēle posmā:
                    <span class="ssb-accent font-semibold">{{ $nextMatchStage }}</span>
                </p>
            @endif

            @if ($competition->teams->count() >= 2)
                <form method="POST" action="{{ route('admin.competitions.matches.store', $competition) }}" class="grid md:grid-cols-2 gap-3 mb-4">
                    @csrf

                    <select name="home_team_id" required class="w-full rounded-lg border border-zinc-700 bg-zinc-900 px-3 py-2 text-zinc-100">
                        <option value="">Komanda A</option>
                        @foreach ($competition->teams as $team)
                            <option value="{{ $team->id }}">{{ $team->name }}</option>
                        @endforeach
                    </select>

                    <select name="away_team_id" required class="w-full rounded-lg border border-zinc-700 bg-zinc-900 px-3 py-2 text-zinc-100">
                        <option value="">Komanda B</option>
                        @foreach ($competition->teams as $team)
                            <option value="{{ $team->id }}">{{ $team->name }}</option>
                        @endforeach
                    </select>

                    <input type="datetime-local" name="played_at" value="{{ old('played_at') }}" class="w-full rounded-lg border border-zinc-700 bg-zinc-900 px-3 py-2 text-zinc-100">

                    <input type="number" name="home_score" min="0" required placeholder="A punkti" class="w-full rounded-lg border border-zinc-700 bg-zinc-900 px-3 py-2 text-zinc-100">
                    <input type="number" name="away_score" min="0" required placeholder="B punkti" class="w-full rounded-lg border border-zinc-700 bg-zinc-900 px-3 py-2 text-zinc-100">

                    <div class="md:col-span-2">
                        <button type="submit" class="ssb-button-primary">Pievienot rezultātu</button>
                    </div>
                </form>
            @else
                <p class="ssb-muted mb-4">Vispirms pievieno vismaz 2 komandas.</p>
            @endif

            @forelse ($competition->matches as $match)
                <p class="ssb-text py-1">
                    • {{ $stageOptions[$match->stage] ?? 'Spēle' }}:
                    {{ $match->homeTeam?->name }} {{ $match->home_score }} : {{ $match->away_score }} {{ $match->awayTeam?->name }}
                    @if ($match->played_at)
                        <span class="ssb-muted">({{ $match->played_at->format('d.m.Y H:i') }})</span>
                    @endif
                </p>
            @empty
                <p class="ssb-muted">Rezultāti vēl nav pievienoti.</p>
            @endforelse
        </section>

        <section class="ssb-card mt-6">
            <h2 class="text-2xl ssb-title mb-4">Turnīra tabula</h2>

            @if ($standings->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left ssb-muted">
                                <th class="py-2 pr-3">Komanda</th>
                                <th class="py-2 pr-3">SP</th>
                                <th class="py-2 pr-3">U</th>
                                <th class="py-2 pr-3">Z</th>
                                <th class="py-2 pr-3">PF</th>
                                <th class="py-2 pr-3">PA</th>
                                <th class="py-2 pr-3">+/-</th>
                                <th class="py-2 pr-0">P</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($standings as $row)
                                <tr class="border-t border-zinc-800/70">
                                    <td class="py-2 pr-3 ssb-text">{{ $row['team']->name }}</td>
                                    <td class="py-2 pr-3 ssb-text">{{ $row['played'] }}</td>
                                    <td class="py-2 pr-3 ssb-text">{{ $row['wins'] }}</td>
                                    <td class="py-2 pr-3 ssb-text">{{ $row['losses'] }}</td>
                                    <td class="py-2 pr-3 ssb-text">{{ $row['points_for'] }}</td>
                                    <td class="py-2 pr-3 ssb-text">{{ $row['points_against'] }}</td>
                                    <td class="py-2 pr-3 ssb-text">{{ $row['diff'] }}</td>
                                    <td class="py-2 pr-0 ssb-accent font-semibold">{{ $row['table_points'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="ssb-muted">Tabula parādīsies pēc komandu/spēļu pievienošanas.</p>
            @endif
        </section>

        <section class="ssb-card mt-6">
            <h2 class="text-2xl ssb-title mb-4">Gala vietas</h2>

            @if ($competition->teams->isNotEmpty())
                <form method="POST" action="{{ route('admin.competitions.placements.update', $competition) }}">
                    @csrf
                    @method('PUT')

                    <div class="space-y-3">
                        @foreach ($competition->teams as $team)
                            <div class="flex items-center gap-3">
                                <label class="ssb-text w-full">{{ $team->name }}</label>
                                <input type="number" min="1" name="positions[{{ $team->id }}]" value="{{ old('positions.'.$team->id, $team->final_position) }}" placeholder="Vieta" class="w-28 rounded-lg border border-zinc-700 bg-zinc-900 px-3 py-2 text-zinc-100">
                            </div>
                        @endforeach
                    </div>

                    <button type="submit" class="ssb-button-primary mt-4">Saglabāt vietas</button>
                </form>
            @else
                <p class="ssb-muted">Vispirms pievieno komandas.</p>
            @endif
        </section>
    </section>
@endsection
