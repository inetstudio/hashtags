<?php

namespace InetStudio\Hashtags\Http\Controllers\Back\Posts;

use App\Http\Controllers\Controller;
use InetStudio\Hashtags\Contracts\Services\Back\Posts\PostsDataTableServiceContract;
use InetStudio\Hashtags\Contracts\Http\Controllers\Back\Posts\PostsDataControllerContract;

/**
 * Class PostsDataController.
 */
class PostsDataController extends Controller implements PostsDataControllerContract
{
    /**
     * Получаем данные для отображения в таблице.
     *
     * @param PostsDataTableServiceContract $dataTableService
     *
     * @return mixed
     */
    public function data(PostsDataTableServiceContract $dataTableService)
    {
        return $dataTableService->ajax();
    }
}
