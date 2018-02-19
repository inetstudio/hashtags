<?php

namespace InetStudio\Hashtags\Http\Controllers\Back\Prizes;

use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use InetStudio\Hashtags\Models\PrizeModel;
use InetStudio\Hashtags\Contracts\Http\Requests\Back\Prizes\SavePrizeRequestContract;
use InetStudio\Hashtags\Contracts\Services\Back\Prizes\PrizesDataTableServiceContract;
use InetStudio\Hashtags\Contracts\Http\Controllers\Back\Prizes\PrizesControllerContract;

/**
 * Class PrizesController.
 */
class PrizesController extends Controller implements PrizesControllerContract
{
    /**
     * Список призов.
     *
     * @param PrizesDataTableServiceContract $dataTableService
     *
     * @return View
     */
    public function index(PrizesDataTableServiceContract $dataTableService): View
    {
        $table = $dataTableService->html();

        return view('admin.module.hashtags::back.pages.prizes.index', compact('table'));
    }

    /**
     * Добавление приза.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(): View
    {
        return view('admin.module.hashtags::back.pages.prizes.form', [
            'item' => new PrizeModel(),
        ]);
    }

    /**
     * Создание приза.
     *
     * @param SavePrizeRequestContract $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(SavePrizeRequestContract $request): RedirectResponse
    {
        return $this->save($request);
    }

    /**
     * Редактирование приза.
     *
     * @param null $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id = null): View
    {
        if (! is_null($id) && $id > 0 && $item = PrizeModel::find($id)) {
            return view('admin.module.hashtags::back.pages.prizes.form', [
                'item' => $item,
            ]);
        } else {
            abort(404);
        }
    }

    /**
     * Обновление приза.
     *
     * @param SavePrizeRequestContract $request
     * @param null $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(SavePrizeRequestContract $request, $id = null): RedirectResponse
    {
        return $this->save($request, $id);
    }

    /**
     * Сохранение приза.
     *
     * @param SavePrizeRequestContract $request
     * @param null $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    private function save(SavePrizeRequestContract $request, $id = null): RedirectResponse
    {
        if (! is_null($id) && $id > 0 && $item = PrizeModel::find($id)) {
            $action = 'отредактирован';
        } else {
            $action = 'создан';
            $item = new PrizeModel();
        }

        $item->name = trim(strip_tags($request->get('name')));
        $item->alias = trim(strip_tags($request->get('alias')));
        $item->description = trim($request->input('description.text'));
        $item->save();

        event(app()->makeWith('InetStudio\Hashtags\Contracts\Events\Prizes\ModifyPrizeEventContract', ['object' => $item]));

        Session::flash('success', 'Приз «'.$item->name.'» успешно '.$action);

        return response()->redirectToRoute('back.hashtags.prizes.edit', [
            $item->fresh()->id,
        ]);
    }

    /**
     * Удаление приза.
     *
     * @param null $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id = null): JsonResponse
    {
        if (! is_null($id) && $id > 0 && $item = PrizeModel::find($id)) {
            event(app()->makeWith('InetStudio\Hashtags\Contracts\Events\Prizes\ModifyPrizeEventContract', ['object' => $item]));

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
