@extends('layouts.app')

@section('content')
    <section class="max-w-3xl mx-auto">
        <h1 class="text-3xl ssb-title mb-2">Pieteikt komandu</h1>
        <p class="ssb-muted mb-6">Aizpildi formu, un komandas pieteikums tiks nosūtīts adminam apstiprināšanai.</p>

        @if (session('status'))
            <div class="ssb-card mb-4">
                <p class="ssb-text">{{ session('status') }}</p>
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

        <form method="POST" action="{{ route('team-applications.store') }}" enctype="multipart/form-data" class="ssb-card space-y-4">
            @csrf

            <div>
                <label for="name" class="block text-sm ssb-muted mb-1">Komandas nosaukums</label>
                <input id="name" name="name" value="{{ old('name') }}" required class="ssb-form-control">
            </div>

            <div>
                <label for="logo" class="block text-sm ssb-muted mb-1">Komandas logo (nav obligāts)</label>
                <input id="logo" type="file" name="logo" accept="image/*" class="ssb-form-control">
            </div>

            <div>
                <label for="players" class="block text-sm ssb-muted mb-1">Spēlētāji (katru jaunā rindā)</label>
                <textarea id="players" name="players" rows="8" required class="ssb-form-control">{{ old('players') }}</textarea>
                <p class="text-sm ssb-muted mt-1">Piemērs: Jānis Bērziņš, nākamajā rindā Kārlis Liepiņš utt.</p>
            </div>

            <button type="submit" class="ssb-button-primary">Izveidot komandu</button>
        </form>
    </section>
@endsection
