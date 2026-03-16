@extends('layouts.app')

@section('content')
    <section class="max-w-3xl mx-auto">
        <h1 class="text-3xl ssb-title mb-2">Admin profils</h1>
        <p class="ssb-muted mb-6">Sacensību rīkotāja konts.</p>

        <article class="ssb-card space-y-3">
            <p class="ssb-text"><strong>Vārds:</strong> {{ auth()->user()->name }}</p>
            <p class="ssb-text"><strong>E-pasts:</strong> {{ auth()->user()->email }}</p>
            <p class="ssb-text"><strong>Loma:</strong> Admin (rīkotājs)</p>

            <div class="flex flex-wrap gap-3 pt-2">
                <a href="{{ route('admin.news.index') }}" class="ssb-button-primary">Pārvaldīt jaunumus</a>
                <a href="{{ route('admin.competitions.index') }}" class="ssb-button-secondary">Pārvaldīt sacensības</a>
                <a href="{{ route('admin.teams.index') }}" class="ssb-button-secondary">Pārvaldīt komandas</a>
            </div>

            <form method="POST" action="{{ route('logout') }}" class="pt-2">
                @csrf
                <button type="submit" class="ssb-button-secondary">Izlogoties</button>
            </form>
        </article>
    </section>
@endsection
