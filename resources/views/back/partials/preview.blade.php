@if ($media['type'] == 'video')
    @if ($media['source'])
        <a data-fancybox="gallery-{{ $gallery }}" href="#fancy-{{ $id }}" class="fancybox-video-link">
            <img data-src="{{ ($media['preview']) ? $media['preview'] : $media['placeholder'] }}" class="m-b-md img-responsive {{ ($media['preview']) ? '' : 'placeholder' }}" alt="post_image">
        </a>
        <div id="fancy-{{ $id }}" class="fancybox-video">
            <video controls width="100%" height="auto">
                <source src="{{ $media['source'] }}" type="video/mp4">
            </video>
        </div>
    @else
        <img data-src="{{ ($media['preview']) ? $media['preview'] : $media['placeholder'] }}" class="m-b-md img-responsive {{ ($media['preview']) ? '' : 'placeholder' }}" alt="post_image">
    @endif
@else
    @if ($media['source'])
        <a data-fancybox="gallery-{{ $gallery }}" href="{{ $media['source'] }}">
            <img data-src="{{ ($media['preview']) ? $media['preview'] : $media['placeholder'] }}" class="m-b-md img-responsive {{ ($media['preview']) ? '' : 'placeholder' }}" alt="post_image">
        </a>
    @else
        <img data-src="{{ ($media['preview']) ? $media['preview'] : $media['placeholder'] }}" class="m-b-md img-responsive {{ ($media['preview']) ? '' : 'placeholder' }}" alt="post_image">
    @endif
@endif
