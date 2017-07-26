@php
    $blockStatus = \InetStudio\Hashtags\Models\StatusModel::where('block', true)->first();
@endphp

<p><strong>Социальная сеть:</strong> {{ $item['network'] }}</p>
<p><strong>Тип поста:</strong> {{ $item['media']['type'] }}</p>
<p><strong>Пользователь:</strong> <a href="{{ $item['user']['link'] }}" target="_blank">{{ $item['user']['name'] }}</a>
    @if ($blockStatus->alias and $item['status'] != $blockStatus->alias)
        <a href="{{ route('back.hashtags.posts.moderate', [$item['id'], $blockStatus->alias]) }}" class="btn btn-xs btn-outline btn-danger block-post"><i class="fa fa-minus-circle"></i></a>
    @endif
</p>
<p><strong>Пост:</strong> <a href="{{ $item['post']['link'] }}" target="_blank">Перейти</a></p>
<p><strong>ID поста на сайте:</strong> <span id="hash-{{ $item['id'] }}">{{ $item['hash'] }}</span> <button class="btn btn-white btn-xs clipboard" data-clipboard-target="#hash-{{ $item['id'] }}"><i class="fa fa-copy"></i></button></p>
<p><strong>Содержимое:</strong><br/>{!! $item['media']['caption'] !!}</p>
