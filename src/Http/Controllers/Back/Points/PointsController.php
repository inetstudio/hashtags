<?php

namespace InetStudio\Hashtags\Http\Controllers\Back\Points;

use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use InetStudio\Hashtags\Models\PointModel;
use InetStudio\Hashtags\Http\Requests\Back\SavePointRequest;
use InetStudio\AdminPanel\Http\Controllers\Back\Traits\DatatablesTrait;

/**
 * Class PointsController
 * @package InetStudio\Hashtags\Http\Controllers\Back\Points
 */
class PointsController extends Controller
{
    use DatatablesTrait;

    /**
     * Список баллов.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     *
     * @throws \Exception
     */
    public function index(): View
    {
        $table = $this->generateTable('hashtags', 'points');

        return view('admin.module.hashtags::back.pages.points.index', compact('table'));
    }

    /**
     * Добавление баллов.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(): View
    {
        return view('admin.module.hashtags::back.pages.points.form', [
            'item' => new PointModel(),
        ]);
    }

    /**
     * Создание баллов.
     *
     * @param SavePointRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(SavePointRequest $request): RedirectResponse
    {
        return $this->save($request);
    }

    /**
     * Редактирование баллов.
     *
     * @param null $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id = null): View
    {
        if (! is_null($id) && $id > 0 && $item = PointModel::find($id)) {
            return view('admin.module.hashtags::back.pages.points.form', [
                'item' => $item,
            ]);
        } else {
            abort(404);
        }
    }

    /**
     * Обновление баллов.
     *
     * @param SavePointRequest $request
     * @param null $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(SavePointRequest $request, $id = null): RedirectResponse
    {
        return $this->save($request, $id);
    }

    /**
     * Сохранение баллов.
     *
     * @param $request
     * @param null $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    private function save($request, $id = null): RedirectResponse
    {
        if (! is_null($id) && $id > 0 && $item = PointModel::find($id)) {
            $action = 'отредактированы';
        } else {
            $action = 'созданы';
            $item = new PointModel();
        }

        $item->name = trim(strip_tags($request->get('name')));
        $item->alias = trim(strip_tags($request->get('alias')));
        $item->numeric = trim(strip_tags($request->get('numeric')));
        $item->show = ($request->filled('show')) ? true : false;
        $item->save();

        Session::flash('success', 'Баллы «'.$item->name.'» успешно '.$action);

        return response()->redirectToRoute('back.hashtags.points.edit', [
            $item->fresh()->id,
        ]);
    }

    /**
     * Удаление баллов.
     *
     * @param null $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id = null): JsonResponse
    {
        if (! is_null($id) && $id > 0 && $item = PointModel::find($id)) {
            $item->delete();

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
