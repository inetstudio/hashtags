<?php

namespace InetStudio\Hashtags\Http\Controllers\Back\Statuses;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use InetStudio\Hashtags\Models\StatusModel;

/**
 * Class StatusesUtilityController
 * @package InetStudio\Hashtags\Http\Controllers\Back\Statuses
 */
class StatusesUtilityController extends Controller
{
    /**
     * Возвращаем статусы для поля.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSuggestions(Request $request): JsonResponse
    {
        $search = $request->get('q');

        $items = StatusModel::select(['id', 'name'])->where('name', 'LIKE', '%'.$search.'%')->get();

        if ($request->filled('type') and $request->get('type') == 'autocomplete') {
            $type = get_class(new StatusModel());

            $data = $items->mapToGroups(function ($item) use ($type) {
                return [
                    'suggestions' => [
                        'value' => $item->name,
                        'data' => [
                            'id' => $item->id,
                            'type' => $type,
                            'title' => $item->name,
                        ],
                    ],
                ];
            });
        } else {
            $data = $items->mapToGroups(function ($item) {
                return [
                    'items' => [
                        'id' => $item->id,
                        'name' => $item->name,
                    ],
                ];
            });
        }

        return response()->json($data);
    }
}
