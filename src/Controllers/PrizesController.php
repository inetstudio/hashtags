<?php

namespace InetStudio\Hashtags\Controllers;

use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use InetStudio\Hashtags\Models\PrizeModel;
use InetStudio\Hashtags\Requests\SavePrizeRequest;
use InetStudio\Hashtags\Transformers\PrizeTransformer;

/**
 * Контроллер для управления призами.
 *
 * Class ContestByTagStatusesController
 */
class PrizesController extends Controller
{
    /**
     * Список призов.
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
            ['data' => 'created_at', 'name' => 'created_at', 'title' => 'Дата создания'],
            ['data' => 'updated_at', 'name' => 'updated_at', 'title' => 'Дата обновления'],
            ['data' => 'actions', 'name' => 'actions', 'title' => 'Действия'],
        ]);

        $table->ajax([
            'url' => route('back.hashtags.prizes.data'),
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
                'url' => asset('admin/js/plugins/datatables/locales/russian.lang'),
            ],
        ]);

        return view('admin.module.hashtags::pages.prizes.index', compact('table'));
    }

    /**
     * Добавление приза.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('admin.module.hashtags::pages.prizes.form', [
            'item' => new PrizeModel(),
        ]);
    }

    /**
     * Создание приза.
     *
     * @param SavePrizeRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(SavePrizeRequest $request)
    {
        return $this->save($request);
    }

    /**
     * Редактирование приза.
     *
     * @param null $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id = null)
    {
        if (! is_null($id) && $id > 0) {
            $item = PrizeModel::where('id', '=', $id)->first();
        } else {
            abort(404);
        }

        if (empty($item)) {
            abort(404);
        }

        return view('admin.module.hashtags::pages.prizes.form', [
            'item' => $item,
        ]);
    }

    /**
     * Обновление приза.
     *
     * @param SavePrizeRequest $request
     * @param null $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(SavePrizeRequest $request, $id = null)
    {
        return $this->save($request, $id);
    }

    /**
     * Сохранение приза.
     *
     * @param $request
     * @param null $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    private function save($request, $id = null)
    {
        if (! is_null($id) && $id > 0) {
            $edit = true;
            $item = PrizeModel::where('id', '=', $id)->first();

            if (empty($item)) {
                abort(404);
            }
        } else {
            $edit = false;
            $item = new PrizeModel();
        }

        $params = [
            'name' => trim(strip_tags($request->get('name'))),
            'alias' => trim(strip_tags($request->get('alias'))),
            'description' => trim(strip_tags($request->get('description'))),
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
        Session::flash('success', 'Приз «'.$item->name.'» успешно '.$action);

        return redirect()->to(route('back.hashtags.prizes.edit', $item->fresh()->id));
    }

    /**
     * Удаление приза.
     *
     * @param null $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id = null)
    {
        if (! is_null($id) && $id > 0) {
            $item = PrizeModel::where('id', '=', $id)->first();
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
        $items = PrizeModel::query();

        return Datatables::of($items)
            ->setTransformer(new PrizeTransformer)
            ->escapeColumns(['actions'])
            ->make();
    }
}
