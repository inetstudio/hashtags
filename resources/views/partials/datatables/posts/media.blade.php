@if ($item['media']['type'] == 'video')
    <a data-fancybox="{{ $item['status'] }}" href="#fancy-{{ $item['id'] }}" class="fancybox-video-link">
        <img src="{{ $item['media']['preview'] }}" class=" m-b-md img-responsive" alt="post_image">
    </a>
    <div id="fancy-{{ $item['id'] }}" class="fancybox-video">
        <video controls width="100%" height="auto">
            <source src="{{ $item['media']['source'] }}" type="video/mp4">
        </video>
    </div>
@else
    <a data-fancybox="{{ $item['status'] }}" href="{{ $item['media']['source'] }}">
        <img src="{{ $item['media']['preview'] }}" class=" m-b-md img-responsive" alt="post_image">
    </a>
@endif
