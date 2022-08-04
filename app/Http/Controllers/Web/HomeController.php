<?php

namespace App\Http\Controllers\Web;

use App\Entities\Post;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index()
    {
        $news = $this->getHomePageNews();

        return view('web.home')->with('news', $news);
    }

    private function getHomePageNews()
    {
        $query = Post::query();
        $query->where('status', 1)
            ->orderByDesc('created_at');

        return $query->paginate(request('per_page', 3));
    }
}
