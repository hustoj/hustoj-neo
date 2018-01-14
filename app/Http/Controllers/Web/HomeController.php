<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Repositories\Criteria\Where;
use App\Repositories\PostRepository;
use Czim\Repository\Criteria\Common\OrderBy;

class HomeController extends Controller
{
    public function index()
    {
        $news = $this->getHomePageNews();

        return view('web.home')->with('news', $news);
    }

    private function getHomePageNews()
    {
        /** @var PostRepository $repo */
        $repo = app(PostRepository::class);
        $repo->pushCriteria(new Where('status', 0));
        $repo->pushCriteria(new OrderBy('created_at', 'desc'));

        return $repo->paginate(request('per_page', 3));
    }
}
