<?php

namespace InetStudio\Hashtags\Http\Controllers\Back\Stages;

use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use InetStudio\Hashtags\Models\StageModel;
use InetStudio\Hashtags\Http\Requests\Back\SaveStageRequest;
use InetStudio\AdminPanel\Http\Controllers\Back\Traits\DatatablesTrait;

/**
 * Class StagesController
 * @package InetStudio\Hashtags\Http\Controllers\Back\Stages
 */
class StagesController extends Controller
{
    use DatatablesTrait;

    /**
     * Список этапов.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     *
     * @throws \Exception
     */
    public function index(): View
    {
        $table = $this->generateTable('hashtags', 'stages');

        return view('admin.module.hashtags::back.pages.stages.index', compact('table'));
    }

    /**
     * Добавление этапа.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(): View
    {
        return view('admin.module.hashtags::back.pages.stages.form', [
            'item' => new StageModel(),
        ]);
    }

    /**
     * Создание этапа.
     *
     * @param SaveStageRequest $request
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(SaveStageRequest $request): RedirectResponse
    {
        return $this->save($request);
    }

    /**
     * Редактирование этапа.
     *
     * @param null $id
     * 
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id = null): View
    {
        if (! is_null($id) && $id > 0 && $item = StageModel::find($id)) {
            return view('admin.module.hashtags::back.pages.stages.form', [
                'item' => $item,
            ]);
        } else {
            abort(404);
        }
    }

    /**
     * Обновление этапа.
     *
     * @param SaveStageRequest $request
     * @param null $id
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(SaveStageRequest $request, $id = null): RedirectResponse
    {
        return $this->save($request, $id);
    }

    /**
     * Сохранение этапа.
     *
     * @param $request
     * @param null $id
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    private function save($request, $id = null): RedirectResponse
    {
        if (! is_null($id) && $id > 0 && $item = StageModel::find($id)) {
            $action = 'отредактирован';
        } else {
            $action = 'создан';
            $item = new StageModel();
        }

        $item->name = trim(strip_tags($request->get('name')));
        $item->alias = trim(strip_tags($request->get('alias')));
        $item->description = trim($request->input('description.text'));
        $item->save();

        Session::flash('success', 'Этап «'.$item->name.'» успешно '.$action);

        return response()->redirectToRoute('back.hashtags.stages.edit', [
            $item->fresh()->id,
        ]);
    }

    /**
     * Удаление этапа.
     *
     * @param null $id
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id = null): JsonResponse
    {
        if (! is_null($id) && $id > 0 && $item = StageModel::find($id)) {
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
