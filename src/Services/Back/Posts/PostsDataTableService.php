<?php

namespace InetStudio\Hashtags\Services\Back\Posts;

use League\Fractal\Manager;
use Yajra\DataTables\DataTables;
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Services\DataTable;
use InetStudio\Hashtags\Models\PostModel;
use InetStudio\Hashtags\Models\StatusModel;
use League\Fractal\Serializer\DataArraySerializer;
use InetStudio\Hashtags\Contracts\Services\Back\Posts\PostsDataTableServiceContract;

/**
 * Class PostsDataTableService.
 */
class PostsDataTableService extends DataTable implements PostsDataTableServiceContract
{
    /**
     * Запрос на получение данных таблицы.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Exception
     */
    public function ajax()
    {
        return DataTables::of($this->query())
            ->rawColumns(['media', 'info', 'submit', 'prizes', 'statuses', 'actions'])
            ->make();
    }

    /**
     * Get the query object to be processed by dataTables.
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder|\Illuminate\Support\Collection
     */
    public function query()
    {
        $statusId = $this->request()->get('status_id');
        $status = StatusModel::find($statusId);

        if (! $status) {
            abort(404);
        }

        $items = PostModel::with([
                'status' => function ($statusQuery) {
                    $statusQuery->select(['id', 'alias']);
                },
                'social' => function ($socialQuery) {
                    $socialQuery->with([
                        'media' => function ($mediaQuery) {
                            $mediaQuery->select(['id', 'model_id', 'model_type', 'collection_name', 'file_name', 'disk']);
                        },
                        'user',
                    ])
                        ->select([
                            'id', 'type',
                        ]);
                },
            ])
            ->select(['id', 'hash', 'social_id', 'social_type', 'status_id'])
            ->where('status_id', $statusId)
            ->withTrashed()->get();

        $transformer = app()->make('InetStudio\Hashtags\Contracts\Transformers\Back\Posts\PostTransformerContract');

        $resource = $transformer->transformCollection($items);

        return Collect($this->serializeToArray($resource));
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html(): Builder
    {
        $table = app('datatables.html');

        return $table
            ->columns($this->getColumns())
            ->ajax($this->getAjaxOptions())
            ->parameters($this->getParameters());
    }

    /**
     * Получаем колонки.
     *
     * @return array
     */
    protected function getColumns(): array
    {
        return [
            ['data' => 'id', 'name' => 'id', 'title' => 'ID', 'orderable' => true],
            ['data' => 'media', 'name' => 'media', 'title' => 'Медиа', 'orderable' => false, 'searchable' => false],
            ['data' => 'info', 'name' => 'info', 'title' => 'Инфо', 'orderable' => false, 'searchable' => true],
            ['data' => 'date', 'name' => 'date', 'title' => 'Дата создания', 'orderable' => true, 'searchable' => true, 'orderData' => 4],
            ['data' => 'orderDate', 'name' => 'orderDate', 'title' => 'Дата создания (сортировка)', 'orderable' => true, 'visible' => false],
            ['data' => 'prizes', 'name' => 'prizes', 'title' => 'Призы', 'orderable' => false, 'searchable' => true],
            ['data' => 'submit', 'name' => 'submit', 'title' => 'Подтверждение', 'orderable' => false, 'searchable' => false],
            ['data' => 'statuses', 'name' => 'statuses', 'title' => 'Модерация', 'orderable' => false, 'searchable' => false],
            ['data' => 'actions', 'name' => 'actions', 'title' => 'Действия', 'orderable' => false, 'searchable' => false],
        ];
    }

    /**
     * Свойства ajax datatables.
     *
     * @return array
     */
    protected function getAjaxOptions(): array
    {
        return [
            'url' => route('back.hashtags.posts.data.index'),
            'type' => 'POST',
            'data' => 'function (data) {  
                data.status_id = $("#currentStatus").val();
            }',
        ];
    }

    /**
     * Свойства datatables.
     *
     * @return array
     */
    protected function getParameters(): array
    {
        $i18n = trans('admin::datatables');

        return [
            'paging' => true,
            'pagingType' => 'full_numbers',
            'searching' => true,
            'info' => false,
            'searchDelay' => 350,
            'language' => $i18n,
        ];
    }

    /**
     * Преобразовываем данные в массив.
     *
     * @param $resource
     *
     * @return array
     */
    private function serializeToArray($resource): array
    {
        $manager = new Manager();
        $manager->setSerializer(new DataArraySerializer());

        $transformation = $manager->createData($resource)->toArray();

        return $transformation['data'];
    }
}
