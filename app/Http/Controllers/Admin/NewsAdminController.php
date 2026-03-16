<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsPost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class NewsAdminController extends Controller
{
    public function index(): View
    {
        $newsPosts = NewsPost::query()->orderByDesc('published_at')->get();

        return view('admin.news.index', compact('newsPosts'));
    }

    public function create(): View
    {
        return view('admin.news.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedNewsData($request);

        if ($request->hasFile('image')) {
            $data['image_path'] = $this->storeImage($request->file('image'));
        }

        NewsPost::create($data);

        return redirect()->route('admin.news.index');
    }

    public function edit(NewsPost $newsPost): View
    {
        return view('admin.news.edit', compact('newsPost'));
    }

    public function update(Request $request, NewsPost $newsPost): RedirectResponse
    {
        $data = $this->validatedNewsData($request, $newsPost);

        if ($request->hasFile('image')) {
            $this->deleteImage($newsPost->image_path);
            $data['image_path'] = $this->storeImage($request->file('image'));
        }

        $newsPost->update($data);

        return redirect()->route('admin.news.index');
    }

    public function destroy(NewsPost $newsPost): RedirectResponse
    {
        $this->deleteImage($newsPost->image_path);
        $newsPost->delete();

        return redirect()->route('admin.news.index');
    }

    private function storeImage(UploadedFile $image): string
    {
        $directory = public_path('media/news-images');

        if (! File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $fileName = Str::uuid().'.'.$image->getClientOriginalExtension();
        $image->move($directory, $fileName);

        return 'media/news-images/'.$fileName;
    }

    private function deleteImage(?string $imagePath): void
    {
        if (! $imagePath) {
            return;
        }

        $fullPath = public_path($imagePath);

        if (File::exists($fullPath)) {
            File::delete($fullPath);
        }
    }

    private function newsRules(?NewsPost $newsPost = null): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('news_posts', 'slug')->ignore($newsPost?->id),
            ],
            'excerpt' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'image' => ['nullable', 'image', 'max:4096'],
            'published_at' => ['nullable', 'date'],
            'is_published' => ['nullable', 'boolean'],
        ];
    }

    private function validatedNewsData(Request $request, ?NewsPost $newsPost = null): array
    {
        $data = $request->validate($this->newsRules($newsPost));
        $data['slug'] = Str::slug($data['slug'] ?: $data['title']);
        $data['is_published'] = $request->boolean('is_published');

        return $data;
    }
}
