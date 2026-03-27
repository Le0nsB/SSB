@extends('layouts.app')

@section('content')
    <section class="max-w-5xl mx-auto">
        <div class="mb-6">
            <h1 class="text-3xl ssb-title mb-2">Admin — Komandu pieteikumi</h1>
            <p class="ssb-muted">Apstiprini vai noraidi komandu pieteikumus.</p>
        </div>

        @if (session('status'))
            <div class="ssb-card mb-4">
                <p class="ssb-text">{{ session('status') }}</p>
            </div>
        @endif

        @if (session('error'))
            <div class="ssb-card mb-4">
                <p class="ssb-text">{{ session('error') }}</p>
            </div>
        @endif

        @if ($errors->any())
            <div class="ssb-card mb-4">
                <p class="ssb-subtitle">Lūdzu izlabo kļūdas:</p>
                <ul class="mt-2 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li class="ssb-text">• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @forelse ($teamApplications as $application)
            <article class="ssb-card mb-4">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-semibold ssb-text">{{ $application->name }}</h2>
                        <p class="text-sm ssb-muted mt-1">Statuss: {{ $application->status }}</p>
                    </div>

                    @if ($application->logo_path)
                        <img src="{{ asset('storage/'.$application->logo_path) }}" alt="{{ $application->name }} logo" class="h-16 w-16 rounded-lg object-cover border border-zinc-700">
                    @endif
                </div>

                <div class="mt-4">
                    <p class="text-sm ssb-muted mb-2">Spēlētāji</p>
                    <ul class="grid gap-1 sm:grid-cols-2">
                        @foreach (($application->players ?? []) as $player)
                            <li class="ssb-text">• {{ $player }}</li>
                        @endforeach
                    </ul>
                </div>

                @if ($application->status === 'pending')
                    <div class="mt-4 flex flex-wrap gap-3">
                        <form method="POST" action="{{ route('admin.team-applications.approve', $application) }}">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="ssb-button-primary">Apstiprināt</button>
                        </form>

                        <form method="POST" action="{{ route('admin.team-applications.reject', $application) }}" class="flex-1 min-w-[16rem]">
                            @csrf
                            @method('PUT')
                            <div class="flex flex-wrap gap-2">
                                <input
                                    type="text"
                                    name="rejection_reason"
                                    placeholder="Noraidījuma iemesls (nav obligāts)"
                                    class="ssb-form-control flex-1"
                                >
                                <button type="submit" class="ssb-button-secondary">Noraidīt</button>
                            </div>
                        </form>
                    </div>
                @else
                    <p class="text-sm ssb-muted mt-4">
                        Apstrādāts: {{ optional($application->reviewed_at)->format('d.m.Y H:i') ?? '—' }}
                        @if ($application->rejection_reason)
                            • Iemesls: {{ $application->rejection_reason }}
                        @endif
                    </p>
                @endif
            </article>
        @empty
            <p class="ssb-muted">Nav neviena komandas pieteikuma.</p>
        @endforelse
    </section>
@endsection
