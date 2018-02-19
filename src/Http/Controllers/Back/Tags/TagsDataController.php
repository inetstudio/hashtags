<?php

namespace InetStudio\Hashtags\Http\Controllers\Back\Tags;

use App\Http\Controllers\Controller;
use InetStudio\Hashtags\Contracts\Services\Back\Tags\TagsDataTableServiceContract;
use InetStudio\Hashtags\Contracts\Http\Controllers\Back\Tags\TagsDataControllerContract;

/**
 * Class TagsDataController.
 */
class TagsDataController extends Controller implements TagsDataControllerContract
{
    /**
     * Получаем данные для отображения в таблице.
     *
     * @param TagsDataTableServiceContract $dataTableService
     *
     * @return mixed
     */
    public function data(TagsDataTableServiceContract $dataTableService)
    {
        return $dataTableService->ajax();
    }
}
