@extends('layouts.app')

@section('content')
    <section class="max-w-3xl mx-auto">
        <h1 class="text-3xl ssb-title mb-6">Rediģēt jaunumu</h1>

        <form method="POST" action="{{ route('admin.news.update', $newsPost) }}" enctype="multipart/form-data" class="ssb-card space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm ssb-muted mb-1">Virsraksts</label>
                <input name="title" value="{{ old('title', $newsPost->title) }}" required class="w-full rounded-lg border border-zinc-700 bg-zinc-900 px-3 py-2 text-zinc-100">
            </div>

            <div>
                <label class="block text-sm ssb-muted mb-1">Slug</label>
                <input name="slug" value="{{ old('slug', $newsPost->slug) }}" class="w-full rounded-lg border border-zinc-700 bg-zinc-900 px-3 py-2 text-zinc-100">
            </div>

            <div>
                <label class="block text-sm ssb-muted mb-1">Īsais apraksts</label>
                <input name="excerpt" value="{{ old('excerpt', $newsPost->excerpt) }}" class="w-full rounded-lg border border-zinc-700 bg-zinc-900 px-3 py-2 text-zinc-100">
            </div>

            <div>
                <label class="block text-sm ssb-muted mb-1">Saturs</label>
                <textarea name="content" rows="6" required class="w-full rounded-lg border border-zinc-700 bg-zinc-900 px-3 py-2 text-zinc-100">{{ old('content', $newsPost->content) }}</textarea>
            </div>

            <div>
                <label class="block text-sm ssb-muted mb-1">Bilde (pēc izvēles)</label>
                <input type="file" name="image" accept="image/*" class="w-full rounded-lg border border-zinc-700 bg-zinc-900 px-3 py-2 text-zinc-100">
                @if ($newsPost->image_path)
                    <img src="{{ asset($newsPost->image_path) }}" alt="{{ $newsPost->title }}" class="mt-3 w-full max-w-sm rounded-lg object-cover">
                @endif
            </div>

            <div>
                <label class="block text-sm ssb-muted mb-1">Publicēšanas datums</label>
                <input type="datetime-local" name="published_at" value="{{ old('published_at', optional($newsPost->published_at)->format('Y-m-d\\TH:i')) }}" class="w-full rounded-lg border border-zinc-700 bg-zinc-900 px-3 py-2 text-zinc-100">
            </div>

            <label class="flex items-center gap-2 ssb-text">
                <input type="checkbox" name="is_published" value="1" {{ old('is_published', $newsPost->is_published) ? 'checked' : '' }}>
                Publicēts
            </label>

            <div class="flex flex-wrap items-center gap-3">
                <button type="submit" class="ssb-button-primary">Saglabāt izmaiņas</button>
            </div>
        </form>

        <form method="POST" action="{{ route('admin.news.destroy', $newsPost) }}" class="mt-3" onsubmit="return confirm('Vai tiešām dzēst šo jaunumu?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="ssb-button-secondary">Dzēst jaunumu</button>
        </form>
    </section>
@endsection
