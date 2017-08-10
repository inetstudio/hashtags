<?php

namespace InetStudio\Hashtags\Controllers;

use Ramsey\Uuid\Uuid;
use Illuminate\Http\Request;
use Emojione\Emojione as Emoji;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use InetStudio\Hashtags\Models\TagModel;
use InetStudio\Hashtags\Models\PostModel;
use InetStudio\Hashtags\Models\PointModel;
use InetStudio\Hashtags\Models\StageModel;
use InetStudio\Hashtags\Models\StatusModel;
use InetStudio\Hashtags\Requests\SavePostRequest;
use InetStudio\Hashtags\Transformers\PostTransformer;

/**
 * Контроллер для управления постами.
 *
 * Class ContestByTagStatusesController
 */
class PostsController extends Controller
{
    /**
     * Список конкурсных постов.
     *
     * @param Datatables $dataTable
     * @param string $status
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Datatables $dataTable, $status = '')
    {
        if ($status == '') {
            $status = StatusModel::where('default', true)->first();
        } else {
            $status = StatusModel::where('alias', '=', $status)->first();
        }

        if (empty($status)) {
            abort(404);
        }

        $statuses = StatusModel::select('alias', 'name')->get()->pluck('name', 'alias')->toArray();

        $postsSort = PostModel::with('social')->withTrashed()->where('status_id', $status->id)->orderBy('position', 'desc')->get();

        $sortItems = [];

        foreach ($postsSort as $post) {
            $postData = \Cache::remember('postsSort'.$post->id, 1440, function () use ($post, $status) {
                return [
                    'id' => $post->id,
                    'media' => [
                        'type' => $post->social->type,
                        'preview' => asset($post->social->getFirstMedia('images')->getUrl('admin_index_thumb')),
                        'source' => ($post->social->type == 'video') ? asset($post->social->getFirstMediaUrl('videos')) : asset($post->social->getFirstMediaUrl('images')),
                    ],
                    'user' => [
                        'name' => $post->social->user->user_nickname,
                        'link' => $post->social->user->user_url,
                    ],
                ];
            });

            $sortItems[] = $postData;
        }

        $table = $dataTable->getHtmlBuilder();

        $columns = [
            ['data' => 'id', 'name' => 'id', 'title' => 'ID', 'orderable' => true],
            ['data' => 'media', 'name' => 'media', 'title' => 'Медиа', 'orderable' => false, 'searchable' => false],
            ['data' => 'info', 'name' => 'info', 'title' => 'Инфо', 'orderable' => false, 'searchable' => false],
        ];

        if (! $status->main) {
            $columns = array_merge($columns, [
                ['data' => 'submit', 'name' => 'submit', 'title' => 'Подтверждение', 'orderable' => false, 'searchable' => false],
            ]);
        }

        $columns = array_merge($columns, [
            ['data' => 'statuses', 'name' => 'statuses', 'title' => 'Модерация', 'orderable' => false, 'searchable' => false],
            ['data' => 'actions', 'name' => 'actions', 'title' => 'Действия', 'orderable' => false, 'searchable' => false],
        ]);

        $table->columns($columns);

        $table->ajax([
            'url' => route('back.hashtags.posts.data'),
            'type' => 'POST',
            'data' => 'function(data) { 
                data._token = $(\'meta[name="csrf-token"]\').attr(\'content\'); 
                data.status_id = "'.$status->id.'";
            }',
        ]);

        $table->parameters([
            'paging' => true,
            'pagingType' => 'full_numbers',
            'searching' => true,
            'info' => false,
            'searchDelay' => 350,
            'language' => [
                'url' => asset('admin/js/plugins/datatables/locales/russian.json'),
            ],
        ]);

        return view('admin.module.hashtags::pages.posts.index', [
            'statuses' => $statuses,
            'currentStatus' => $status->alias,
            'sortItems' => $sortItems,
            'table' => $table,
        ]);
    }

    /**
     * Datatables serverside.
     *
     * @param Request $request
     * @return mixed
     */
    public function data(Request $request)
    {
        $statusId = $request->get('status_id');
        $status = StatusModel::where('id', $statusId)->first();

        if (empty($status)) {
            abort(404);
        }

        $items = PostModel::with(['social', 'social.user'])->where('status_id', $statusId)->withTrashed();

        if ($request->has('search.value')) {
            $items = $items->get();
        }

        $datatables = Datatables::of($items)
            ->setTransformer(new PostTransformer($status))
            ->escapeColumns(['media', 'info', 'statuses', 'actions'])
            ->orderColumn('id', '-id $1');

        if ($request->has('search.value')) {
            $datatables->filter(function ($engine) use ($request) {
                $search = $request->input('search.value');
                $collection = $engine->collection;

                $engine->collection = $collection->filter(function ($item) use ($search) {
                    return strpos($item['info'], $search) !== false;
                });
            });
        }

        return $datatables->make();
    }

    /**
     * Редактирование конкурсного поста.
     *
     * @param null $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id = null)
    {
        if (! is_null($id) && $id > 0) {
            $item = PostModel::where('id', '=', $id)->withTrashed()->first();
        } else {
            abort(404);
        }

        if (empty($item)) {
            abort(404);
        }

        return view('admin.module.hashtags::pages.posts.form', [
            'item' => $item,
        ]);
    }

    /**
     * Обновление конкурсного поста.
     *
     * @param SavePostRequest $request
     * @param null $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(SavePostRequest $request, $id = null)
    {
        return $this->save($request, $id);
    }

    /**
     * Сохранение конкурсного поста.
     *
     * @param $request
     * @param null $id
     * @return \Illuminate\Http\RedirectResponse
     */
    private function save($request, $id = null)
    {
        if (! is_null($id) && $id > 0) {
            $item = PostModel::where('id', '=', $id)->withTrashed()->first();

            if (empty($item)) {
                abort(404);
            }
        }

        $params = [
            'status_id' => trim(strip_tags($request->get('status_id'))),
            'stage_id' => ($request->has('stage_id')) ? trim(strip_tags($request->get('stage_id'))) : 0,
            'prize_id' => ($request->has('prize_id')) ? trim(strip_tags($request->get('prize_id'))) : 0,
            'last_editor_id' => Auth::id(),
        ];

        $mainStatus = StatusModel::where('main', true)->first();
        $deleteStatus = StatusModel::where('delete', true)->first();
        $blockStatus = StatusModel::where('block', true)->first();

        if ($item->status->id == $mainStatus->id && $item->status->id != trim(strip_tags($request->get('status_id')))) {
            foreach ($item->tags as $tag) {
                $tag->points()->wherePivot('post_id', $item->id)->detach();
                $item->points()->wherePivot('tag_id', $tag->id)->detach();
            }
            $item->tags()->detach();

            if ($blockStatus->id == trim(strip_tags($request->get('status_id')))) {
                $userSocialPosts = $item->social->user->posts;
                $userPosts = PostModel::whereIn('social_id', $userSocialPosts->pluck('id')->toArray())->where('social_type', get_class($userSocialPosts->first()))->get();

                foreach ($userPosts as $userPost) {
                    foreach ($userPost->tags as $tag) {
                        $tag->points()->wherePivot('post_id', $userPost->id)->detach();
                        $item->points()->wherePivot('tag_id', $tag->id)->detach();
                    }
                    $userPost->tags()->detach();
                    $userPost->update([
                        'status_id' => $blockStatus->id,
                    ]);
                }
            }
        }

        $item->fill($params);
        $item->save();

        if ($item->fresh()->status->id == $deleteStatus->id) {
            $item->delete();
        } else {
            $item->restore();
        }

        Session::flash('success', 'Пост успешно отредактирован');

        if ($item->trashed()) {
            return redirect()->to(route('back.hashtags.posts.index'));
        } else {
            return redirect()->to(route('back.hashtags.posts.edit', $item->fresh()->id));
        }
    }

    public function add(Request $request)
    {
        $network = $request->get('social_network');
        $link = $request->get('post_link');
        
        switch ($network) {
            case 'Instagram':
                $urlFragments = explode('/', trim($link, '/'));
                $code = end($urlFragments);
                $id = \InstagramID::fromCode($code);

                $igPost = \InstagramPost::createPost($id);
                if (! isset($igPost)) {
                    return;
                }

                $igUser = \InstagramUser::createUser($igPost->user_pk);
                if (! isset($igUser)) {
                    return;
                }

                $igPost->addMediaFromUrl($igPost->image_versions)->toMediaCollection('images', 'instagram_posts');
                $igUser->addMediaFromUrl($igUser->profile_pic_url)->toMediaCollection('images', 'instagram_users');

                if ($igPost->media_type == 2) {
                    $igPost->addMediaFromUrl($igPost->video_versions)->toMediaCollection('videos', 'instagram_posts');
                }

                $uuid = Uuid::uuid4();

                $statuses = StatusModel::where('default', true)->get();
                if ($statuses->count() != 1) {
                    $statuses = StatusModel::orderBy('id', 'asc')->get();
                }

                $status = $statuses->first();

                PostModel::create([
                    'hash' => $uuid->toString(),
                    'status_id' => isset($status->id) ? $status->id : 0,
                    'social_id' => $igPost->id,
                    'social_type' => get_class($igPost),
                ]);

                break;
        }

        return redirect()->to(route('back.hashtags.posts.index'));
    }

    /**
     * Удаление конкурсного поста.
     *
     * @param null $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id = null)
    {
        if (! is_null($id) && $id > 0) {
            $item = PostModel::where('id', '=', $id)->first();
        } else {
            return response()->json([
                'success' => false,
            ]);
        }

        if (empty($item)) {
            return response()->json([
                'success' => false,
            ]);
        }

        $status = StatusModel::where('delete', true)->first();

        if (! empty($status)) {
            $item->update([
                'status_id' => $status->id,
            ]);

            $item->tags()->detach();
        }

        $item->delete();

        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * Модерация поста.
     *
     * @param Request $request
     * @param string $id
     * @param $status
     * @return \Illuminate\Http\RedirectResponse
     */
    public function moderate(Request $request, $id, $status)
    {
        if (is_null($id) || $id < 1 || is_null($status)) {
            abort(404);
        }

        $item = PostModel::where('id', '=', $id)->withTrashed()->first();
        $status = StatusModel::where('alias', '=', $status)->first();

        if (empty($item) || empty($status)) {
            abort(404);
        }

        if ($status->main) {
            if ($request->has('tag_id')) {
                $tag = TagModel::where('id', '=', (int) trim($request->get('tag_id')))->first();
            }

            if ($request->has('points_id')) {
                $points = PointModel::where('id', '=', (int) trim($request->get('points_id')))->first();

                if ($points) {
                    if (isset($tag)) {
                        $tag->points()->attach($points, ['post_id' => $item->id]);
                        $item->tags()->attach($tag, ['point_id' => $points->id]);
                    }

                    $item->points()->attach($points, ['tag_id' => (isset($tag)) ? $tag->id : 0]);
                }
            } else {
                if (isset($tag)) {
                    $item->tags()->attach($tag);
                }
            }
        } else {
            foreach ($item->tags as $tag) {
                $tag->points()->wherePivot('post_id', $item->id)->detach();
                $item->points()->wherePivot('tag_id', $tag->id)->detach();
            }
            $item->tags()->detach();

            if ($status->block) {
                $userSocialPosts = $item->social->user->posts;
                $userPosts = PostModel::whereIn('social_id', $userSocialPosts->pluck('id')->toArray())->where('social_type', get_class($userSocialPosts->first()))->get();

                foreach ($userPosts as $userPost) {
                    foreach ($userPost->tags as $tag) {
                        $tag->points()->wherePivot('post_id', $userPost->id)->detach();
                        $item->points()->wherePivot('tag_id', $tag->id)->detach();
                    }
                    $userPost->tags()->detach();
                    $userPost->update([
                        'status_id' => $status->id,
                    ]);
                }
            }
        }

        $item->update([
            'status_id' => $status->id,
        ]);

        if ($item->trashed() && ! $status->delete) {
            $item->restore();
        } elseif ($status->delete) {
            $item->delete();
        }

        return redirect()->back();
    }

    /**
     * Скачиваем архив работ.
     *
     * @param $status
     * @param $id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download($status, $id = null)
    {
        if (! is_null($status)) {
            $status = StatusModel::where('alias', '=', $status)->first();

            if (empty($status)) {
                abort(404);
            }
        }

        $select = PostModel::where('status_id', '=', $status->id)->withTrashed();

        if (! is_null($id) && $id > 0) {
            $select->where('id', '=', $id);
        }

        $posts = $select->search(request()->get('search'))->get();

        $excelFname = time();
        \Excel::create($excelFname, function ($excel) use ($posts) {
            $excel->sheet('Посты', function ($sheet) use ($posts) {
                $sheet->row(1, [
                    'ID', 'Социальная сеть', 'Пользователь', 'Ссылка на профиль', 'Ссылка на пост', 'Ссылка на медиа', 'Имя файла',
                ]);

                foreach ($posts as $index => $post) {
                    $type = ($post->social->hasMedia('videos')) ? 'video' : 'photo';
                    $fileUrl = ($type == 'photo') ? $post->social->getFirstMediaUrl('images') : $post->social->getFirstMediaUrl('videos');
                    $fileName = ($type == 'photo') ? $post->social->getFirstMedia('images')->file_name : $post->social->getFirstMedia('videos')->file_name;

                    $sheet->appendRow([
                        $post->hash, $post->social->social_name, $post->social->user->user_nickname, $post->social->user->user_url, $post->social->post_url, url($fileUrl), $fileName,
                    ]);
                }
            });
        })->store('xlsx', public_path('storage/contest/downloads'));

        $fname = 'storage/contest/downloads/'.time().'.zip';
        $zip = \Zipper::make($fname);
        $zip->add(public_path('storage/contest/downloads/'.$excelFname.'.xlsx'));

        foreach ($posts as $post) {
            $type = ($post->social->hasMedia('videos')) ? 'video' : 'photo';
            $filePath = ($type == 'photo') ? $post->social->getFirstMediaPath('images') : $post->social->getFirstMediaPath('videos');
            $zip->folder('posts')->add($filePath);
        }
        $zip->close();

        return response()->download(public_path($fname));
    }

    public function getGallery(Request $request, $social = '')
    {
        $items = \Cache::remember('gallery'.md5($request->get('tag_id').$request->get('tag_name').$social), 60, function () use ($request, $social) {
            $mainStatuses = StatusModel::select('id')->where('main', true)->pluck('id')->toArray();
            $visiblePoints = PointModel::select('id')->where('show', true)->pluck('id')->toArray();

            $items = PostModel::with('social')->whereIn('status_id', $mainStatuses)->orderBy('position', 'desc')->orderBy('id', 'desc');

            if ($request->has('tag_id') or $request->has('tag_name')) {
                $tagId = trim($request->get('tag_id'));
                $tagName = trim($request->get('tag_name'));

                $tag = TagModel::where('id', '=', $tagId)->get();

                if ($tag->count() == 0) {
                    $tag = TagModel::where('name', '=', $tagName)->get();
                }

                if ($tag->count() > 0) {
                    $tag = $tag->first();

                    if (PointModel::count() > 0) {
                        $items = $tag->posts()->wherePivotIn('point_id', $visiblePoints)->get();
                    } else {
                        $items = $tag->posts()->get();
                    }
                } else {
                    if (PointModel::count() > 0) {
                        $items = $items->whereHas('points', function ($query) use ($visiblePoints) {
                            $query->whereIn('hashtags_points.id', $visiblePoints);
                        })->get();
                    } else {
                        $items = $items->get();
                    }
                }
            } else {
                if (PointModel::count() > 0) {
                    $items = $items->whereHas('points', function ($query) use ($visiblePoints) {
                        $query->whereIn('hashtags_points.id', $visiblePoints);
                    })->get();
                } else {
                    $items = $items->get();
                }
            }

            $items = $items->map(function ($item) use ($social) {
                $points = (int) $item->points()->sum('numeric');

                if ($social == '' or ($item->social->social_name == $social)) {
                    return [
                        'id' => $item->id,
                        'type' => $item->social->type,
                        'thumb' => url($item->social->getFirstMedia('images')->getUrl(config('hashtags.gallery_preview_images').'_thumb')),
                        'src' => ($item->social->hasMedia('videos')) ? url($item->social->getFirstMediaUrl('videos')) : url($item->social->getFirstMediaUrl('images')),
                        'username' => $item->social->user->user_nickname,
                        'caption' => Emoji::shortnameToUnicode($item->social->caption),
                        'points' => $points,
                        'pointsWord' => $this->getPointsWord($points),
                        'tags' => $item->tags()->select(['hashtags_tags.id as id', 'hashtags_tags.name as name'])->pluck('name', 'id')->toArray(),
                    ];
                }
            });

            return array_values(array_filter($items->toArray()));
        });

        $data['stop'] = false;

        if ($request->has('page') and $request->has('limit')) {
            $total = count($items);
            $page = $request->get('page');
            $limit = $request->get('limit');

            $offset = ($page - 1) * $limit;
            $items = array_slice($items, $offset, $limit);

            $data['stop'] = (($page + 1) * $limit >= $total) ? true : false;
        }

        $data['items'] = $items;

        return response()->json($data, 200);
    }

    /*
    public function getDayWinners(Request $request)
    {
        $items = \Cache::remember('dayWinners', 60, function() use ($request) {
            $items = PostModel::whereHas('status', function ($query) {
                $query->where('alias', 'approved');
            })->whereNotNull('prize_date')->orderBy('prize_date', 'asc')->get();

            $items = $items->map(function ($item) {
                $currentPoints = ContestByCityTagPointModel::where('post_id', '=', $item->id)->get();

                $points = ($currentPoints->count() > 0) ? $currentPoints->last()->points : 0;

                if ($points != 3) {
                    return [
                        'id' => $item->id,
                        'photo' => url($item->social->getFirstMedia('images')->getUrl(config('contestByCityTag.winners_preview_images').'_thumb')),
                        'photoBig' => url($item->social->getFirstMediaUrl('images')),
                        'src' => ($item->social->hasMedia('videos')) ? url($item->social->getFirstMediaUrl('videos')) : url($item->social->getFirstMediaUrl('images')),
                        'authorName' => $item->social->user->userNickname,
                        'ball' => $points.' '.$this->getPointsWord($points),
                        'city' => $item->city->name,
                        'cityId' => $item->city->id,
                        'day' => date('j', strtotime($item->prize_date)).' '.$this->getMonthName(date('n', strtotime($item->prize_date))),
                    ];
                }
            });

            return array_values(array_filter($items->toArray()));
        });

        return response()->json($items, 200);
    }
    */

    public function getStagesWinners(Request $request, $stageAlias = '')
    {
        $items = \Cache::remember('stagesWinners_'.$stageAlias, 60, function () use ($request, $stageAlias) {
            $mainStatuses = StatusModel::select('id')->where('main', true)->pluck('id')->toArray();

            $stages = StageModel::select(['id', 'name', 'alias']);

            if ($stageAlias) {
                $stages = $stages->where('alias', $stageAlias)->get();
            } else {
                $stages = $stages->get();
            }

            $items = PostModel::with('social')
                ->whereIn('status_id', $mainStatuses)
                ->where('stage_id', '<>', 0)
                ->orderBy('stage_id', 'asc')
                ->orderBy('prize_id', 'asc')
                ->orderBy('position', 'desc')
                ->orderBy('id', 'desc')->get();

            $items = $items->map(function ($item) {
                $points = (int) $item->points()->sum('numeric');

                return [
                    'stage_id' => $item->stage->id,
                    'prize' => (isset($item->prize)) ? $item->prize->name : '',
                    'id' => $item->id,
                    'type' => $item->social->type,
                    'thumb' => url($item->social->getFirstMedia('images')->getUrl(config('hashtags.gallery_preview_images').'_thumb')),
                    'src' => ($item->social->hasMedia('videos')) ? url($item->social->getFirstMediaUrl('videos')) : url($item->social->getFirstMediaUrl('images')),
                    'username' => $item->social->user->user_nickname,
                    'caption' => Emoji::shortnameToUnicode($item->social->caption),
                    'points' => $points,
                    'pointsWord' => $this->getPointsWord($points),
                    'tags' => $item->tags()->select(['hashtags_tags.id as id', 'hashtags_tags.name as name'])->pluck('name', 'id')->toArray(),
                ];
            });

            $items = array_values(array_filter($items->toArray()));

            $data = [];

            $data['count'] = count($items);
            foreach ($stages as $stage) {
                $data['stages'][$stage->alias]['name'] = $stage->name;

                foreach ($items as $item) {
                    if ($item['stage_id'] == $stage->id) {
                        $data['stages'][$stage->alias]['items'][$item['prize']][] = $item;
                    }
                }
            }

            return $data;
        });

        return response()->json($items, 200);
    }

    public function sort(Request $request)
    {
        $id = $request->get('currentId');

        if (is_null($id) || $id < 1) {
            abort(404);
        }

        $item = PostModel::where('id', '=', $id)->withTrashed()->first();

        if (empty($item)) {
            abort(404);
        }

        if ($request->get('prev') != 0) {
            $itemPrev = PostModel::where('id', '=', $request->get('prev'))->withTrashed()->first();

            if (empty($itemPrev)) {
                abort(404);
            }

            $item->moveAfter($itemPrev);
        } elseif ($request->get('next') != 0) {
            $itemNext = PostModel::where('id', '=', $request->get('next'))->withTrashed()->first();

            if (empty($itemNext)) {
                abort(404);
            }

            $item->moveBefore($itemNext);
        }

        return response()->json([
            'success' => true,
        ]);
    }

    private function get_correct_str($num, $str1, $str2, $str3)
    {
        $val = $num % 100;

        if ($val > 10 && $val < 20) {
            return $str3;
        } else {
            $val = $num % 10;
            if ($val == 1) {
                return $str1;
            } elseif ($val > 1 && $val < 5) {
                return $str2;
            } else {
                return $str3;
            }
        }
    }

    private function getMonthName($monthNumber)
    {
        $month = [1 => 'января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'];

        return (isset($month[$monthNumber])) ? $month[$monthNumber] : '';
    }

    private function getPointsWord($num)
    {
        $num = (! $num) ? 0 : $num;

        return $this->get_correct_str($num, 'балл', 'балла', 'баллов');
    }
}
