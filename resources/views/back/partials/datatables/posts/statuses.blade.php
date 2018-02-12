@php
    $mainStatus = \InetStudio\Hashtags\Models\StatusModel::whereHas('classifiers', function ($classifiersQuery) {
        $classifiersQuery->where('classifiers.alias', 'main');
    })->first();

    $statuses = \InetStudio\Hashtags\Models\StatusModel::select('alias', 'name')->get()->pluck('name', 'alias')->toArray();
@endphp

<div class="btn-group">
    <button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle" aria-expanded="false">Статус <span class="caret"></span></button>
    <ul class="dropdown-menu">
        @foreach ($statuses as $aliasAction => $statusAction)
            @if ($aliasAction != $item['status'] and $aliasAction != $mainStatus->alias)
                <li><a href="{{ route('back.hashtags.posts.moderate', [$item['id'], $aliasAction]) }}">{{ $statusAction }}</a></li>
            @endif
        @endforeach
    </ul>
</div>
