<?php

namespace InetStudio\Hashtags\Http\Controllers\Back\Posts;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use InetStudio\Hashtags\Models\PostModel;
use InetStudio\Hashtags\Models\StatusModel;
use InetStudio\Hashtags\Contracts\Services\Back\ContestPostsServiceContract;
use InetStudio\Instagram\Contracts\Services\Back\InstagramIDServiceContract;
use InetStudio\Instagram\Contracts\Services\Back\InstagramPostsServiceContract;
use InetStudio\Vkontakte\Contracts\Services\Back\VkontaktePostsServiceContract;

/**
 * Class PostsModerationController
 * @package InetStudio\Hashtags\Http\Controllers\Back\Posts
 */
class PostsModerationController extends Controller
{
    private $services = [];

    /**
     * PostsModerationController constructor.
     */
    public function __construct()
    {
        $this->services['InstagramID'] = app()->make(InstagramIDServiceContract::class);
        $this->services['InstagramPosts'] = app()->make(InstagramPostsServiceContract::class);
        $this->services['ContestPosts'] = app()->make(ContestPostsServiceContract::class);
        $this->services['VkontaktePosts'] = app()->make(VkontaktePostsServiceContract::class);
    }

    /**
     * Добавление поста в гелерею.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function add(Request $request): RedirectResponse
    {
        $network = $request->get('social_network');
        $link = $request->get('post_link');

        switch ($network) {
            case 'instagram':
                $urlFragments = explode('/', trim(parse_url($link, PHP_URL_PATH), '/'));
                $code = end($urlFragments);
                $id = $this->services['InstagramID']->fromCode($code);

                $post = $this->services['InstagramPosts']->getPostByID($id);
                $this->services['ContestPosts']->createPostFromInstagram($post);

                break;
            case 'vkontakte':
                $id = str_replace('w=wall', '', parse_url($link, PHP_URL_QUERY));

                $post = $this->services['VkontaktePosts']->getPostByID($id);
                $this->services['ContestPosts']->createPostFromVkontakte($post);

                break;
        }

        return response()->redirectToRoute('back.hashtags.posts.index');
    }

    /**
     * Сортируем посты.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sort(Request $request): JsonResponse
    {
        $id = $request->get('currentId');

        if (! is_null($id) && $id > 0 && $item = PostModel::withTrashed()->find($id)) {
            if ($request->get('prev') != 0) {
                $itemPrev = PostModel::withTrashed()->find($request->get('prev'));

                if (! $itemPrev) {
                    abort(404);
                }

                $item->moveAfter($itemPrev);
            } elseif ($request->get('next') != 0) {
                $itemNext = PostModel::withTrashed()->find($request->get('next'));

                if (! $itemNext) {
                    abort(404);
                }

                $item->moveBefore($itemNext);
            }
        } else {
            abort(404);
        }

        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * Модерация поста.
     *
     * @param Request $request
     * @param string $id
     * @param $statusAlias
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function moderate(Request $request, $id, $statusAlias): RedirectResponse
    {
        if (!(! is_null($id) && $id > 0 && $item = PostModel::withTrashed()->find($id))) {
            abort(404);
        }

        if (!(! is_null($statusAlias) && $status = StatusModel::where('alias', '=', $statusAlias)->first())) {
            abort(404);
        }

        $this->services['ContestPosts']->moveToStatus($request, $item, $status);

        return redirect()->back();
    }
}
