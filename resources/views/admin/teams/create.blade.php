@extends('layouts.app')

@section('content')
    <section class="max-w-3xl mx-auto">
        <h1 class="text-3xl ssb-title mb-6">Pievienot komandu</h1>

        <form method="POST" action="{{ route('admin.teams.store') }}" class="ssb-card space-y-4">
            @csrf

            <div>
                <label class="block text-sm ssb-muted mb-1">Komandas nosaukums</label>
                <input name="name" value="{{ old('name') }}" required class="w-full rounded-lg border border-zinc-700 bg-zinc-900 px-3 py-2 text-zinc-100">
            </div>

            <button type="submit" class="ssb-button-primary">Saglabāt</button>
        </form>
    </section>
@endsection
