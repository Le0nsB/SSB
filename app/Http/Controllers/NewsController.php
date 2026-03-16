<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\LoadsSsbMedia;
use App\Models\NewsPost;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class NewsController extends Controller
{
    use LoadsSsbMedia;

    public function index(): View
    {
        $newsPosts = collect();
        $mediaItems = $this->getSsbMediaItems();

        if (Schema::hasTable('news_posts')) {
            $newsPosts = NewsPost::query()
                ->where('is_published', true)
                ->orderByDesc('published_at')
                ->get();
        }

        if ($newsPosts->isEmpty()) {
            $newsPosts = $this->fallbackNewsPosts();
        }

        $newsMediaByTitle = $this->mapMediaToTitles($mediaItems, [
            'Atvērta pieteikšanās Sunny Streetball #1' => 'video',
            'Publicēts spēļu grafiks' => 'video',
            'MVP balva un konkursi skatītājiem' => 'image',
        ]);

        return view('news.index', [
            'newsPosts' => $newsPosts,
            'mediaItems' => $mediaItems,
            'newsMediaByTitle' => $newsMediaByTitle,
        ]);
    }

    private function fallbackNewsPosts(): \Illuminate\Support\Collection
    {
        return collect([
            (object) [
                'title' => 'Atvērta pieteikšanās Sunny Streetball #1',
                'published_at' => Carbon::now()->subDays(2),
                'excerpt' => 'Pieteikšanās atvērta līdz trešdienai, vietu skaits komandām ir ierobežots.',
            ],
            (object) [
                'title' => 'Publicēts spēļu grafiks',
                'published_at' => Carbon::now()->subDay(),
                'excerpt' => 'Skaties spēļu laikus un ierašanās laikus katrai komandai sacensību dienā.',
            ],
            (object) [
                'title' => 'MVP balva un konkursi skatītājiem',
                'published_at' => Carbon::now()->subHours(6),
                'excerpt' => 'Šajā posmā būs arī metienu konkurss, MVP balva un pārsteiguma aktivitātes.',
            ],
        ]);
    }
}
