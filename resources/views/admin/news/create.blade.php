@extends('layouts.app')

@section('content')
    <section class="max-w-3xl mx-auto">
        <h1 class="text-3xl ssb-title mb-6">Pievienot jaunumu</h1>

        <form method="POST" action="{{ route('admin.news.store') }}" enctype="multipart/form-data" class="ssb-card space-y-4">
            @csrf

            <div>
                <label class="block text-sm ssb-muted mb-1">Virsraksts</label>
                <input name="title" value="{{ old('title') }}" required class="w-full rounded-lg border border-zinc-700 bg-zinc-900 px-3 py-2 text-zinc-100">
            </div>

            <div>
                <label class="block text-sm ssb-muted mb-1">Slug (pēc izvēles)</label>
                <input name="slug" value="{{ old('slug') }}" class="w-full rounded-lg border border-zinc-700 bg-zinc-900 px-3 py-2 text-zinc-100">
            </div>

            <div>
                <label class="block text-sm ssb-muted mb-1">Īsais apraksts</label>
                <input name="excerpt" value="{{ old('excerpt') }}" class="w-full rounded-lg border border-zinc-700 bg-zinc-900 px-3 py-2 text-zinc-100">
            </div>

            <div>
                <label class="block text-sm ssb-muted mb-1">Saturs</label>
                <textarea name="content" rows="6" required class="w-full rounded-lg border border-zinc-700 bg-zinc-900 px-3 py-2 text-zinc-100">{{ old('content') }}</textarea>
            </div>

            <div>
                <label class="block text-sm ssb-muted mb-1">Bilde (pēc izvēles)</label>
                <input type="file" name="image" accept="image/*" class="w-full rounded-lg border border-zinc-700 bg-zinc-900 px-3 py-2 text-zinc-100">
            </div>

            <div>
                <label class="block text-sm ssb-muted mb-1">Publicēšanas datums</label>
                <input type="datetime-local" name="published_at" value="{{ old('published_at') }}" class="w-full rounded-lg border border-zinc-700 bg-zinc-900 px-3 py-2 text-zinc-100">
            </div>

            <label class="flex items-center gap-2 ssb-text">
                <input type="checkbox" name="is_published" value="1" {{ old('is_published', true) ? 'checked' : '' }}>
                Publicēt uzreiz
            </label>

            <button type="submit" class="ssb-button-primary">Saglabāt</button>
        </form>
    </section>
@endsection
