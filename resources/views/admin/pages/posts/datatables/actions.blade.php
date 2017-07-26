<div class="btn-group">
    @if (! $item['trashed'])
        <a href="{{ route('back.hashtags.posts.edit', [$item['id']]) }}" class="btn btn-xs btn-default m-r"><i class="fa fa-pencil"></i></a>
    @endif
    <a href="{{ route('back.hashtags.posts.download', [$item['status'], $item['id']]) }}" class="btn btn-xs btn-default m-r"><i class="fa fa-download"></i></a>
    @if (! $item['trashed'])
        <a href="#" class="btn btn-xs btn-danger delete" data-url="{{ route('back.hashtags.posts.destroy', [$item['id']]) }}"><i class="fa fa-times"></i></a>
    @endif
</div>
