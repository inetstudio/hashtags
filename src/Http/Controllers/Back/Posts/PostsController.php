<?php

namespace InetStudio\Hashtags\Http\Controllers\Back\Posts;

use Carbon\Carbon;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use InetStudio\Hashtags\Models\PostModel;
use InetStudio\Hashtags\Models\PrizeModel;
use InetStudio\Hashtags\Models\StatusModel;
use InetStudio\Hashtags\Contracts\Http\Requests\Back\Posts\SavePostRequestContract;
use InetStudio\Hashtags\Contracts\Services\Back\Posts\PostsDataTableServiceContract;
use InetStudio\Hashtags\Contracts\Http\Controllers\Back\Posts\PostsControllerContract;

/**
 * Class PostsController.
 */
class PostsController extends Controller implements PostsControllerContract
{
    private $services = [];

    /**
     * PostsController constructor.
     */
    public function __construct()
    {
        $this->services['ContestPosts'] = app()->make('InetStudio\Hashtags\Contracts\Services\Back\Posts\ContestPostsServiceContract');
    }

    /**
     * Список постов.
     *
     * @param PostsDataTableServiceContract $dataTableService
     * @param string $status
     *
     * @return View
     */
    public function index(PostsDataTableServiceContract $dataTableService, $status = ''): View
    {
        $alias = ($status) ? $status : 'moderation';
        $status = StatusModel::where('alias', $alias)->first();

        if (empty($status)) {
            abort(404);
        }

        $statuses = StatusModel::select('alias', 'name')->get()->pluck('name', 'alias')->toArray();

        $postsSort = PostModel::with([
            'social' => function ($socialQuery) {
                $socialQuery->with([
                    'media' => function ($mediaQuery) {
                        $mediaQuery->select(['id', 'model_id', 'model_type', 'collection_name', 'file_name', 'disk']);
                    },
                    'user',
                ])
                ->select([
                    'id', 'type',
                ]);
            }
        ])->withTrashed()->select(['id', 'social_id', 'social_type', 'status_id', 'position'])->where('status_id', $status->id)->orderBy('position', 'desc')->get();

        $sortItems = $postsSort->map(function ($post) {

            $images = ($post->social->hasMedia('images')) ? $post->social->getFirstMedia('images') : null;
            $videos = ($post->social->hasMedia('videos')) ? $post->social->getFirstMedia('videos') : null;

            return [
                'id' => $post->id,
                'media' => [
                    'type' => $post->social->type,
                    'preview' => ($images) ? asset($images->getUrl('preview_admin_index')) : '',
                    'source' => (! ($images || $videos)) ? '' : (($post->social->type == 'video') ? asset($post->social->getFirstMediaUrl('videos')) : asset($post->social->getFirstMediaUrl('images'))),
                    'placeholder' => 'holder.js/200px100?auto=yes&font=FontAwesome&text=&#xf1c5;',
                ],
                'user' => [
                    'name' => $post->social->user->user_nickname,
                    'link' => $post->social->user->user_url,
                ],
            ];
        });

        $table = $dataTableService->html();

        return view('admin.module.hashtags::back.pages.posts.index', [
            'statuses' => $statuses,
            'currentStatus' => $status,
            'sortItems' => $sortItems,
            'table' => $table,
        ]);
    }

    /**
     * Редактирование конкурсного поста.
     *
     * @param null $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id = null): View
    {
        if (! is_null($id) && $id > 0 && $item = PostModel::withTrashed()->find($id)) {
            $images = ($item->social->hasMedia('images')) ? $item->social->getFirstMedia('images') : null;
            $videos = ($item->social->hasMedia('videos')) ? $item->social->getFirstMedia('videos') : null;

            $media = [
                'type' => $item->social->type,
                'preview' => ($images) ? asset($images->getUrl('preview_admin_index')) : '',
                'source' => (! ($images || $videos)) ? '' : (($item->social->type == 'video') ? asset($videos->getUrl()) : asset($images->getUrl())),
                'placeholder' => 'holder.js/96x96?auto=yes&font=FontAwesome&text=&#xf1c5;',
            ];

            return view('admin.module.hashtags::back.pages.posts.form', [
                'item' => $item,
                'media' => $media,
            ]);
        } else {
            abort(404);
        }
    }

    /**
     * Обновление конкурсного поста.
     *
     * @param SavePostRequestContract $request
     * @param null $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(SavePostRequestContract $request, $id = null): RedirectResponse
    {
        return $this->save($request, $id);
    }

    /**
     * Сохранение конкурсного поста.
     *
     * @param SavePostRequestContract $request
     * @param null $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    private function save(SavePostRequestContract $request, $id = null): RedirectResponse
    {
        if (! is_null($id) && $id > 0 && $item = PostModel::withTrashed()->find($id)) {
            $action = 'отредактирован';
        } else {
            abort(404);
        }

        $status = StatusModel::find($request->get('status_id'));

        if (! $status) {
            abort(404);
        }

        $item->prizes()->detach();

        if ($request->filled('prize_type')) {
            foreach ($request->get('prize_type') as $index => $prizeType) {
                $prize = PrizeModel::find($request->get('prize_id')[$index]);

                switch ($prizeType) {
                    case 'winner':
                        $item->prizes()->attach($prize, ['stage_id' => 0, 'date' => null]);
                        break;
                    case 'stage':
                        $item->prizes()->attach($prize, ['stage_id' => $request->get('stage_id')[$index], 'date' => null]);
                        break;
                    case 'day':
                        $date = Carbon::createFromFormat('d.m.Y', $request->get('date')[$index]);

                        $item->prizes()->attach($prize, ['stage_id' => 0, 'date' => $date]);
                        break;
                }
            }
        }

        event(app()->makeWith('InetStudio\Hashtags\Contracts\Events\Posts\ModifyPostEventContract', ['object' => $item]));

        $item = $this->services['ContestPosts']->moveToStatus($request, $item, $status);

        Session::flash('success', 'Пост успешно '.$action);

        if ($item->trashed()) {
            return response()->redirectToRoute('back.hashtags.posts.index');
        } else {
            return response()->redirectToRoute('back.hashtags.posts.edit', [
                $item->fresh()->id,
            ]);
        }
    }

    /**
     * Удаление конкурсного поста.
     *
     * @param Request $request
     * @param null $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id = null): JsonResponse
    {
        $status = StatusModel::where('alias', 'deleted')->first();

        if ($status && ! is_null($id) && $id > 0 && $item = PostModel::find($id)) {
            $this->services['ContestPosts']->moveToStatus($request, $item, $status);

            return response()->json([
                'success' => true,
            ]);
        } else {
            return response()->json([
                'success' => false,
            ]);
        }
    }
}
