<?php

namespace InetStudio\Hashtags\Http\Controllers\Front;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Emojione\Emojione as Emoji;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use InetStudio\Hashtags\Models\TagModel;
use InetStudio\Hashtags\Models\PostModel;
use InetStudio\Hashtags\Models\PointModel;
use InetStudio\Hashtags\Models\StageModel;
use InetStudio\Hashtags\Models\StatusModel;

/**
 * Class PostsController
 * @package InetStudio\Hashtags\Http\Controllers\Front
 */
class PostsController extends Controller
{
    /**
     * Возвращаем галерею постов.
     *
     * @param Request $request
     * @param string $social
     *
     * @return JsonResponse
     */
    public function getGallery(Request $request, string $social = ''): JsonResponse
    {
        $cacheKey = 'PostsController_getGallery';

        $items = Cache::tags(['hashtags_posts'])->remember($cacheKey, 60, function () use ($request, $social) {
            $mainStatuses = StatusModel::whereHas('classifiers', function ($classifiersQuery) {
                $classifiersQuery->where('classifiers.alias', 'main');
            })->pluck('id')->toArray();
            $visiblePoints = PointModel::select('id')->where('show', true)->pluck('id')->toArray();

            $items = PostModel::with('social')
                ->whereIn('status_id', $mainStatuses)
                ->orderBy('position', 'desc')
                ->orderBy('id', 'desc');

            if ($request->filled('tag_id')) {
                $tagId = trim($request->get('tag_id'));

                $tag = TagModel::find($tagId);

                if ($tag) {
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
                if ($social == '' or ($item->social->social_name == $social)) {
                    return $this->getPostData($item);
                }
            });

            return array_values(array_filter($items->toArray()));
        });

        $data['stop'] = false;

        if ($request->filled('page') and $request->filled('limit')) {
            $total = count($items);
            $page = $request->get('page');
            $limit = $request->get('limit');

            $offset = ($page - 1) * $limit;
            $items = array_slice($items, $offset, $limit);

            $data['stop'] = (($page) * $limit >= $total) ? true : false;
            $data['total'] = $total;
        }

        $data['items'] = $items;

        return response()->json($data, 200);
    }

    /**
     * Получаем победителей дня.
     *
     * @param Request $request
     * @param string $prizeAlias
     *
     * @return JsonResponse
     */
    public function getDaysWinners(Request $request, string $prizeAlias = ''): JsonResponse
    {
        $cacheKey = 'PostsController_getDaysWinners';

        $items = Cache::remember($cacheKey, 60, function() use ($request, $prizeAlias) {
            $mainStatuses = StatusModel::whereHas('classifiers', function ($classifiersQuery) {
                $classifiersQuery->where('classifiers.alias', 'main');
            })->pluck('id')->toArray();

            $items = PostModel::with('social')
                ->whereIn('status_id', $mainStatuses)
                ->whereHas('prizes', function ($prizesQuery) {
                    $prizesQuery->where('hashtags_posts_prizes.date', '<>', 0);
                })
                ->orderBy('position', 'desc')
                ->orderBy('id', 'desc')
                ->get();

            $items = $items->map(function ($item) {
                return $this->getPostData($item);
            });

            $items = array_values(array_filter($items->toArray()));

            $data = [];

            $data['count'] = 0;

            foreach ($items as $item) {
                $prizes = collect($item['prizes']);

                if ($prizes->contains('type', 'day')) {
                    $prizes = $prizes->where('type', 'day');

                    if ($prizeAlias) {
                        $prizes = $prizes->where('info.alias', $prizeAlias);
                    }

                    foreach ($prizes as $prize) {
                        $data[$prize['day']['date']]['name'] = $prize['day']['format'];
                        $data[$prize['day']['date']]['prizes'][$prize['info']['alias']]['name'] = $prize['info']['name'];
                        $data[$prize['day']['date']]['prizes'][$prize['info']['alias']]['items'][] = $item;

                        $data['count']++;
                    }
                }
            }

            dd($data);

            return $data;
        });

        return response()->json($items, 200);
    }

    /**
     * Возвращаем посты победителей этапов.
     *
     * @param Request $request
     * @param string $stageAlias
     * @param string $prizeAlias
     *
     * @return JsonResponse
     */
    public function getStagesWinners(Request $request, string $stageAlias = '', string $prizeAlias = ''): JsonResponse
    {
        $cacheKey = 'PostsController_getStagesWinners';

        $items = Cache::remember($cacheKey, 60, function () use ($request, $stageAlias, $prizeAlias) {
            $mainStatuses = StatusModel::whereHas('classifiers', function ($classifiersQuery) {
                $classifiersQuery->where('classifiers.alias', 'main');
            })->pluck('id')->toArray();

            $stages = StageModel::select(['id', 'name', 'alias']);

            if ($stageAlias) {
                $stages = $stages->where('alias', $stageAlias)->get();
            } else {
                $stages = $stages->get();
            }

            $items = PostModel::with('social')
                ->whereIn('status_id', $mainStatuses)
                ->whereHas('prizes', function ($prizesQuery) {
                    $prizesQuery->where('hashtags_posts_prizes.stage_id', '<>', 0);
                })
                ->orderBy('position', 'desc')
                ->orderBy('id', 'desc')
                ->get();

            $items = $items->map(function ($item) {
                return $this->getPostData($item);
            });

            $items = array_values(array_filter($items->toArray()));

            $data = [];

            $data['count'] = 0;
            foreach ($stages as $stage) {
                $data['stages'][$stage->alias]['name'] = $stage->name;

                foreach ($items as $item) {
                    $prizes = collect($item['prizes']);

                    if ($prizes->contains('stage.id', $stage->id)) {

                        $prizes = $prizes->where('stage.id', $stage->id);

                        if ($prizeAlias) {
                            $prizes = $prizes->where('info.alias', $prizeAlias);
                        }

                        foreach ($prizes as $prize) {
                            $data['stages'][$stage->alias]['prizes'][$prize['info']['alias']]['name'] = $prize['info']['name'];
                            $data['stages'][$stage->alias]['prizes'][$prize['info']['alias']]['items'][] = $item;

                            $data['count']++;
                        }
                    }
                }
            }

            return $data;
        });

        return response()->json($items, 200);
    }

    /**
     * Получаем информация по посту.
     *
     * @param PostModel $item
     *
     * @return array
     */
    private function getPostData(PostModel $item): array
    {
        $points = (int) $item->points()->sum('numeric');

        $images = ($item->social->hasMedia('images')) ? $item->social->getFirstMedia('images') : null;
        $videos = ($item->social->hasMedia('videos')) ? $item->social->getFirstMedia('videos') : null;

        $prizes = [];

        foreach ($item->prizes as $prize) {
            $prizeData = [];

            $stageId = $prize->pivot->stage_id;
            $date = $prize->pivot->date;

            if ($stageId) {
                $type = 'stage';
            } elseif ($date) {
                $type = 'day';
            } else {
                $type = 'winner';
            }

            $prizeData['type'] = $type;

            switch ($type) {
                case 'stage':
                    $stage = StageModel::find($stageId);
                    $prizeData['stage'] = [
                        'id' => $stage->id,
                        'name' => $stage->name,
                        'alias' => $stage->alias,
                    ];
                    break;
                case 'day':
                    $carbonDate = Carbon::createFromFormat('Y-m-d', $date);
                    $prizeData['day'] = [
                        'date' => $carbonDate->format('d.m.Y'),
                        'format' => $carbonDate->day.' '.$this->getMonthName($carbonDate->month),
                    ];
                    break;
            }

            $prizeData['info'] = [
                'id' => $prize->id,
                'name' => $prize->name,
                'alias' => $prize->alias,
            ];

            $prizes[] = $prizeData;
        }

        return [
            'post' => [
                'id' => $item->id,
                'type' => $item->social->type,
                'thumb' => ($images) ? asset($images->getUrl(config('hashtags.gallery_preview_images'))) : '',
                'src' => (! ($images || $videos)) ? '' : (($item->social->type == 'video') ? asset($videos->getUrl()) : asset($images->getUrl())),
                'caption' => Emoji::shortnameToUnicode($item->social->caption),
            ],
            'user' => [
                'name' => $item->social->user->user_nickname,
                'link' => $item->social->user->user_url,
            ],
            'points' => [
                'count' => $points,
                'word' => $this->getPointsWord($points),
            ],
            'tags' => $item->tags()->select(['hashtags_tags.id as id', 'hashtags_tags.name as name'])->pluck('name', 'id')->toArray(),
            'prizes' => $prizes,
        ];
    }

    /**
     * Получаем слово с окончанием, зависящим от числа.
     *
     * @param int $num
     * @param array $variant
     *
     * @return string
     */
    private function get_correct_str(int $num, array $variant): string
    {
        $val = $num % 100;

        if ($val > 10 && $val < 20) {
            return $variant[2];
        } else {
            $val = $num % 10;
            if ($val == 1) {
                return $variant[0];
            } elseif ($val > 1 && $val < 5) {
                return $variant[1];
            } else {
                return $variant[2];
            }
        }
    }

    /**
     * Получаем имя месяца по его номеру.
     *
     * @param int $monthNumber
     *
     * @return mixed|string
     */
    private function getMonthName(int $monthNumber): string
    {
        $month = [1 => 'января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'];

        return (isset($month[$monthNumber])) ? $month[$monthNumber] : '';
    }

    /**
     * Получаем слово "балл" с окончанием, зависящим от числа.
     *
     * @param int $num
     *
     * @return string
     */
    private function getPointsWord(int $num): string
    {
        $num = (! $num) ? 0 : $num;

        return $this->get_correct_str($num, ['балл', 'балла', 'баллов']);
    }
}
