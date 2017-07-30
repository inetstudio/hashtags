<?php

namespace InetStudio\Hashtags\Controllers;

use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use InetStudio\Hashtags\Models\PointModel;
use InetStudio\Hashtags\Requests\SavePointRequest;
use InetStudio\Hashtags\Transformers\PointTransformer;

/**
 * Контроллер для управления баллами.
 *
 * Class ContestByTagStatusesController
 */
class PointsController extends Controller
{
    /**
     * Список баллов.
     *
     * @param Datatables $dataTable
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Datatables $dataTable)
    {
        $table = $dataTable->getHtmlBuilder();

        $table->columns([
            ['data' => 'name', 'name' => 'name', 'title' => 'Название'],
            ['data' => 'alias', 'name' => 'alias', 'title' => 'Алиас'],
            ['data' => 'numeric', 'name' => 'numeric', 'title' => 'Количество баллов'],
            ['data' => 'created_at', 'name' => 'created_at', 'title' => 'Дата создания'],
            ['data' => 'updated_at', 'name' => 'updated_at', 'title' => 'Дата обновления'],
            ['data' => 'actions', 'name' => 'actions', 'title' => 'Действия', 'orderable' => false, 'searchable' => false],
        ]);

        $table->ajax([
            'url' => route('back.hashtags.points.data'),
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

        return view('admin.module.hashtags::pages.points.index', compact('table'));
    }

    /**
     * Добавление баллов.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('admin.module.hashtags::pages.points.form', [
            'item' => new PointModel(),
        ]);
    }

    /**
     * Создание баллов.
     *
     * @param SavePointRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(SavePointRequest $request)
    {
        return $this->save($request);
    }

    /**
     * Редактирование баллов.
     *
     * @param null $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id = null)
    {
        if (! is_null($id) && $id > 0) {
            $item = PointModel::where('id', '=', $id)->first();
        } else {
            abort(404);
        }

        if (empty($item)) {
            abort(404);
        }

        return view('admin.module.hashtags::pages.points.form', [
            'item' => $item,
        ]);
    }

    /**
     * Обновление баллов.
     *
     * @param SavePointRequest $request
     * @param null $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(SavePointRequest $request, $id = null)
    {
        return $this->save($request, $id);
    }

    /**
     * Сохранение баллов.
     *
     * @param $request
     * @param null $id
     * @return \Illuminate\Http\RedirectResponse
     */
    private function save($request, $id = null)
    {
        if (! is_null($id) && $id > 0) {
            $edit = true;
            $item = PointModel::where('id', '=', $id)->first();

            if (empty($item)) {
                abort(404);
            }
        } else {
            $edit = false;
            $item = new PointModel();
        }

        $params = [
            'name' => trim(strip_tags($request->get('name'))),
            'alias' => trim(strip_tags($request->get('alias'))),
            'numeric' => trim(strip_tags($request->get('numeric'))),
            'show' => ($request->has('show')) ? true : false,
        ];

        if ($edit) {
            $params['last_editor_id'] = Auth::id();
        } else {
            $params['author_id'] = Auth::id();
            $params['last_editor_id'] = $params['author_id'];
        }

        $item->fill($params);
        $item->save();

        $action = ($edit) ? 'отредактирован' : 'создан';
        Session::flash('success', 'Баллы «'.$item->name.'» успешно '.$action);

        return redirect()->to(route('back.hashtags.points.edit', $item->fresh()->id));
    }

    /**
     * Удаление баллов.
     *
     * @param null $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id = null)
    {
        if (! is_null($id) && $id > 0) {
            $item = PointModel::where('id', '=', $id)->first();
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
     * Datatables serverside.
     *
     * @return mixed
     */
    public function data()
    {
        $items = PointModel::query();

        return Datatables::of($items)
            ->setTransformer(new PointTransformer)
            ->escapeColumns(['name', 'actions'])
            ->make();
    }
}
