<?php

namespace InetStudio\Hashtags\Http\Controllers\Back\Tags;

use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use InetStudio\Hashtags\Models\TagModel;
use InetStudio\Hashtags\Http\Requests\Back\SaveTagRequest;
use InetStudio\AdminPanel\Http\Controllers\Back\Traits\DatatablesTrait;

/**
 * Class TagsController
 * @package InetStudio\Hashtags\Http\Controllers\Back\Tags
 */
class TagsController extends Controller
{
    use DatatablesTrait;
    
    /**
     * Список тегов.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     *
     * @throws \Exception
     */
    public function index(): View
    {
        $table = $this->generateTable('hashtags', 'tags');

        return view('admin.module.hashtags::back.pages.tags.index', compact('table'));
    }

    /**
     * Добавление тега.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(): View
    {
        return view('admin.module.hashtags::back.pages.tags.form', [
            'item' => new TagModel(),
        ]);
    }

    /**
     * Создание тега.
     *
     * @param SaveTagRequest $request
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(SaveTagRequest $request): RedirectResponse
    {
        return $this->save($request);
    }

    /**
     * Редактирование тега.
     *
     * @param null $id
     * 
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id = null): View
    {
        if (! is_null($id) && $id > 0 && $item = TagModel::find($id)) {
            return view('admin.module.hashtags::back.pages.tags.form', [
                'item' => $item,
            ]);
        } else {
            abort(404);
        }
    }

    /**
     * Обновление тега.
     *
     * @param SaveTagRequest $request
     * @param null $id
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(SaveTagRequest $request, $id = null): RedirectResponse
    {
        return $this->save($request, $id);
    }

    /**
     * Сохранение тега.
     *
     * @param $request
     * @param null $id
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    private function save($request, $id = null): RedirectResponse
    {
        if (! is_null($id) && $id > 0 && $item = TagModel::find($id)) {
            $action = 'отредактирован';
        } else {
            $action = 'создан';
            $item = new TagModel();
        }

        $item->name = trim(strip_tags($request->get('name')));
        $item->save();

        Session::flash('success', 'Тег «'.$item->name.'» успешно '.$action);

        return response()->redirectToRoute('back.hashtags.tags.edit', [
            $item->fresh()->id,
        ]);
    }

    /**
     * Удаление тега.
     *
     * @param null $id
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id = null): JsonResponse
    {
        if (! is_null($id) && $id > 0 && $item = TagModel::find($id)) {
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
