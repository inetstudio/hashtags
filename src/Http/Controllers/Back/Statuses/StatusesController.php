<?php

namespace InetStudio\Hashtags\Http\Controllers\Back\Statuses;

use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use InetStudio\Hashtags\Models\StatusModel;
use InetStudio\Classifiers\Http\Controllers\Back\Traits\ClassifiersManipulationsTrait;
use InetStudio\Hashtags\Contracts\Http\Requests\Back\Statuses\SaveStatusRequestContract;
use InetStudio\Hashtags\Contracts\Services\Back\Statuses\StatusesDataTableServiceContract;
use InetStudio\Hashtags\Contracts\Http\Controllers\Back\Statuses\StatusesControllerContract;

/**
 * Class StatusesController.
 */
class StatusesController extends Controller implements StatusesControllerContract
{
    use ClassifiersManipulationsTrait;

    /**
     * Список статусов.
     *
     * @param StatusesDataTableServiceContract $dataTableService
     *
     * @return View
     */
    public function index(StatusesDataTableServiceContract $dataTableService): View
    {
        $table = $dataTableService->html();

        return view('admin.module.hashtags::back.pages.statuses.index', compact('table'));
    }

    /**
     * Добавление статуса.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(): View
    {
        return view('admin.module.hashtags::back.pages.statuses.form', [
            'item' => new StatusModel(),
        ]);
    }

    /**
     * Создание статуса.
     *
     * @param SaveStatusRequestContract $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(SaveStatusRequestContract $request): RedirectResponse
    {
        return $this->save($request);
    }

    /**
     * Редактирование статуса.
     *
     * @param null $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id = null): View
    {
        if (! is_null($id) && $id > 0 && $item = StatusModel::find($id)) {
            return view('admin.module.hashtags::back.pages.statuses.form', [
                'item' => $item,
            ]);
        } else {
            abort(404);
        }
    }

    /**
     * Обновление статуса.
     *
     * @param SaveStatusRequestContract $request
     * @param null $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(SaveStatusRequestContract $request, $id = null): RedirectResponse
    {
        return $this->save($request, $id);
    }

    /**
     * Сохранение статуса.
     *
     * @param SaveStatusRequestContract $request
     * @param null $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    private function save(SaveStatusRequestContract $request, $id = null): RedirectResponse
    {
        if (! is_null($id) && $id > 0 && $item = StatusModel::find($id)) {
            $action = 'отредактирован';
        } else {
            $action = 'создан';
            $item = new StatusModel();
        }

        $item->name = trim(strip_tags($request->get('name')));
        $item->alias = trim(strip_tags($request->get('alias')));
        $item->description = trim($request->input('description.text'));
        $item->save();

        $this->saveClassifiers($item, $request);

        event(app()->makeWith('InetStudio\Hashtags\Contracts\Events\Statuses\ModifyStatusEventContract', ['object' => $item]));

        Session::flash('success', 'Статус «'.$item->name.'» успешно '.$action);

        return response()->redirectToRoute('back.hashtags.statuses.edit', [
            $item->fresh()->id,
        ]);
    }

    /**
     * Удаление статуса.
     *
     * @param null $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id = null): JsonResponse
    {
        if (! is_null($id) && $id > 0 && $item = StatusModel::find($id)) {

            event(app()->makeWith('InetStudio\Hashtags\Contracts\Events\Statuses\ModifyStatusEventContract', ['object' => $item]));

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
