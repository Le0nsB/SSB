@extends('layouts.app')

@section('content')
    <section class="max-w-3xl mx-auto">
        <h1 class="text-3xl ssb-title mb-6">Pievienot sacensības</h1>

        <form method="POST" action="{{ route('admin.competitions.store') }}" class="ssb-card space-y-4">
            @csrf

            <div>
                <label class="block text-sm ssb-muted mb-1">Nosaukums</label>
                <input name="title" value="{{ old('title') }}" required class="w-full rounded-lg border border-zinc-700 bg-zinc-900 px-3 py-2 text-zinc-100">
            </div>

            <div>
                <label class="block text-sm ssb-muted mb-1">Vieta</label>
                <input name="location" value="{{ old('location') }}" required class="w-full rounded-lg border border-zinc-700 bg-zinc-900 px-3 py-2 text-zinc-100">
            </div>

            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm ssb-muted mb-1">Datums</label>
                    <input type="date" name="event_date" value="{{ old('event_date') }}" required class="w-full rounded-lg border border-zinc-700 bg-zinc-900 px-3 py-2 text-zinc-100">
                </div>
                <div>
                    <label class="block text-sm ssb-muted mb-1">Pieteikšanās termiņš</label>
                    <input type="date" name="registration_deadline" value="{{ old('registration_deadline') }}" class="w-full rounded-lg border border-zinc-700 bg-zinc-900 px-3 py-2 text-zinc-100">
                </div>
            </div>

            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm ssb-muted mb-1">Komandu skaits</label>
                    <input type="number" min="1" name="team_limit" value="{{ old('team_limit') }}" class="w-full rounded-lg border border-zinc-700 bg-zinc-900 px-3 py-2 text-zinc-100">
                </div>
                <div>
                    <label class="block text-sm ssb-muted mb-1">Dalības maksa (€)</label>
                    <input type="number" min="0" step="0.01" name="entry_fee" value="{{ old('entry_fee') }}" class="w-full rounded-lg border border-zinc-700 bg-zinc-900 px-3 py-2 text-zinc-100">
                </div>
            </div>

            <div>
                <label class="block text-sm ssb-muted mb-1">Apraksts</label>
                <textarea name="description" rows="4" class="w-full rounded-lg border border-zinc-700 bg-zinc-900 px-3 py-2 text-zinc-100">{{ old('description') }}</textarea>
            </div>

            <label class="flex items-center gap-2 ssb-text">
                <input type="checkbox" name="is_published" value="1" {{ old('is_published', true) ? 'checked' : '' }}>
                Publicēt uzreiz
            </label>

            <button type="submit" class="ssb-button-primary">Saglabāt</button>
        </form>
    </section>
@endsection
