<?php

namespace InetStudio\Hashtags\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use InetStudio\Hashtags\Models\TagModel;
use InetStudio\Hashtags\Requests\SaveTagRequest;
use InetStudio\Hashtags\Transformers\TagTransformer;

/**
 * Контроллер для управления тегами.
 *
 * Class ContestByTagStatusesController
 */
class TagsController extends Controller
{
    /**
     * Список тегов.
     *
     * @param Datatables $dataTable
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Datatables $dataTable)
    {
        $table = $dataTable->getHtmlBuilder();

        $table->columns([
            ['data' => 'name', 'name' => 'name', 'title' => 'Название'],
            ['data' => 'created_at', 'name' => 'created_at', 'title' => 'Дата создания'],
            ['data' => 'updated_at', 'name' => 'updated_at', 'title' => 'Дата обновления'],
            ['data' => 'actions', 'name' => 'actions', 'title' => 'Действия', 'orderable' => false, 'searchable' => false],
        ]);

        $table->ajax([
            'url' => route('back.hashtags.tags.data'),
            'type' => 'POST',
            'data' => 'function(data) { data._token = $(\'meta[name="csrf-token"]\').attr(\'content\'); }',
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

        return view('admin.module.hashtags::pages.tags.index', compact('table'));
    }

    /**
     * Добавление тега.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('admin.module.hashtags::pages.tags.form', [
            'item' => new TagModel(),
        ]);
    }

    /**
     * Создание тега.
     *
     * @param SaveTagRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(SaveTagRequest $request)
    {
        return $this->save($request);
    }

    /**
     * Редактирование тега.
     *
     * @param null $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id = null)
    {
        if (! is_null($id) && $id > 0) {
            $item = TagModel::where('id', '=', $id)->first();
        } else {
            abort(404);
        }

        if (empty($item)) {
            abort(404);
        }

        return view('admin.module.hashtags::pages.tags.form', [
            'item' => $item,
        ]);
    }

    /**
     * Обновление тега.
     *
     * @param SaveTagRequest $request
     * @param null $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(SaveTagRequest $request, $id = null)
    {
        return $this->save($request, $id);
    }

    /**
     * Сохранение тега.
     *
     * @param $request
     * @param null $id
     * @return \Illuminate\Http\RedirectResponse
     */
    private function save($request, $id = null)
    {
        if (! is_null($id) && $id > 0) {
            $edit = true;
            $item = TagModel::where('id', '=', $id)->first();

            if (empty($item)) {
                abort(404);
            }
        } else {
            $edit = false;
            $item = new TagModel();
        }

        $params = [
            'name' => trim(strip_tags($request->get('name'))),
        ];

        $item->fill($params);
        $item->save();

        $action = ($edit) ? 'отредактирован' : 'создан';
        Session::flash('success', 'Тег «'.$item->name.'» успешно '.$action);

        return redirect()->to(route('back.hashtags.tags.edit', $item->fresh()->id));
    }

    /**
     * Удаление тега.
     *
     * @param null $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id = null)
    {
        if (! is_null($id) && $id > 0) {
            $item = TagModel::where('id', '=', $id)->first();
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

        $item->delete();

        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * Поиск тегов.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchTag(Request $request)
    {
        $query = $request->get('query');

        $tags = TagModel::select('id', 'name')->where('name', 'LIKE', $query.'%')->get();

        $data['suggestions'] = [];

        foreach ($tags as $tag) {
            $data['suggestions'][] = [
                'value' => $tag->name,
                'data' => $tag->id,
            ];
        }

        return response()->json($data);
    }

    /*
    public function info(Request $request)
    {
        $tagId = trim($request->get('id'));
        $tagName = trim($request->get('tag'));

        $tag = TagModel::where('id', '=', $tagId)->get();

        if ($tag->count() == 0) {
            $tag = TagModel::where('name', '=', $tagName)->get();
        }



        if ($tag->count() > 0) {
            $tag = $tag->first();

            $points = (! empty($city)) ? $city->points : 0;
            $rating = TagModel::orderBy('points', 'desc')->where('id', '<>', $cityId)->get()->take(3)->reverse();
        } else {
            $city = null;
            $points = 0;
            $rating = TagModel::orderBy('points', 'desc')->get()->take(3)->reverse();
        }

        $delta = 0;
        foreach ($rating as $top) {
            if ($points < $top->points) {
                $delta = ($top->points - $points);
                break;
            }
        }

        if ($city) {
            if ($city->id != TagModel::orderBy('points', 'desc')->get()->take(1)->first()->id) {
                $delta++;
            }
        } else {
            $delta++;
        }

        $data = [];
        $data[] = [
            'id' => ($city) ? $city->id : $request->get('id'),
            'city' => ($city) ? $city->name : $request->get('city'),
            'rating' => $delta,
            'rating_word' => $this->getPointsWord($delta),
        ];

        return response()->json($data);
    }

    private function get_correct_str($num, $str1, $str2, $str3) {
        $val = $num % 100;

        if ($val > 10 && $val < 20) return $str3;
        else {
            $val = $num % 10;
            if ($val == 1) return $str1;
            elseif ($val > 1 && $val < 5) return $str2;
            else return $str3;
        }
    }

    private function getPointsWord($num) {
        $num = (!$num) ? 0 : $num;
        return $this->get_correct_str($num, 'балл', 'балла', 'баллов');
    }

    public function vote(Request $request)
    {
        if (Auth::guest()) {
            return;
        }

        $user = Auth::user();

        $tagId = trim($request->get('id'));
        $tagName = trim($request->get('tag'));

        $tag = TagModel::where('id', '=', $tagId)->get();

        if ($tag->count() == 0) {
            $tag = TagModel::where('name', '=', $tagName)->get();
        }

        if ($tag->count() == 0) {
            return;
        } else {
            $tag = $tag->first();
        }

        if (ContestByCityTagPointModel::where('user_id', '=', $user->id)->count() > 0) {
            $points = ContestByCityTagPointModel::where('user_id', '=', $user->id)->get()->first();
            $address = TagModel::where('id', $points->address_id)->get();

            $data = [];
            $data[] = [
                'id' => ($address->count() > 0) ? $address->first()->id : '',
                'city' => ($address->count() > 0) ? $address->first()->name : '',
                'gerlNamber' => ''
            ];

            return response()->json($data);
        }


        HistoryModel::create([
            'post_id' => 0,
            'tag_id' => $tag->id,
            'user_id' => $user->id,
            'event' => 'Голосование на сайте',
            'points' => 1,
            'datetime' => time(),
        ]);

        $data = [];
        $data[] = [
            'id' => ($tag) ? $tag->id : $tagId,
            'city' => ($tag) ? $tag->name : $tagName,
            'count' => 0,
        ];

        return response()->json($data);
    }
    */

    /**
     * Datatables serverside.
     *
     * @return mixed
     */
    public function data()
    {
        $items = TagModel::query();

        return Datatables::of($items)
            ->setTransformer(new TagTransformer)
            ->escapeColumns(['actions'])
            ->make();
    }
}
