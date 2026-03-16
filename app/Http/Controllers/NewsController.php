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
        $mediaItems = collect();
        $newsMediaByTitle = [];

        if (Schema::hasTable('news_posts')) {
            $newsPosts = NewsPost::query()
                ->where('is_published', true)
                ->orderByDesc('published_at')
                ->get();
        }

        if ($newsPosts->isEmpty()) {
            $newsPosts = collect([
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

        $mediaItems = $this->getSsbMediaItems();

        if ($mediaItems->isNotEmpty()) {

            $videos = $mediaItems->where('kind', 'video')->values();
            $images = $mediaItems->where('kind', 'image')->values();

            $newsMediaByTitle = [
                'Atvērta pieteikšanās Sunny Streetball #1' => $videos->get(0),
                'Publicēts spēļu grafiks' => $videos->get(1),
                'MVP balva un konkursi skatītājiem' => $images->get(0),
            ];
        }

        return view('news.index', [
            'newsPosts' => $newsPosts,
            'mediaItems' => $mediaItems,
            'newsMediaByTitle' => $newsMediaByTitle,
        ]);
    }
}
