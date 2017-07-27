<li class="{{ isActiveMatch('back/hashtags') }}">
    <a href="#"><i class="fa fa-slack"></i> <span class="nav-label">Посты по хэштегам</span><span class="fa arrow"></span></a>
    <ul class="nav nav-second-level collapse">
        <li class="{{ isActiveMatch('back/hashtags/posts') }}">
            <a href="{{ route('back.hashtags.posts.index') }}">Посты</a>
        </li>
        <li class="{{ isActiveMatch('back/hashtags/tags') }}">
            <a href="{{ route('back.hashtags.tags.index') }}">Теги</a>
        </li>
        <li class="{{ isActiveMatch('back/hashtags/prizes') }}">
            <a href="{{ route('back.hashtags.prizes.index') }}">Призы</a>
        </li>
        <li class="{{ isActiveMatch('back/hashtags/statuses') }}">
            <a href="{{ route('back.hashtags.statuses.index') }}">Статусы</a>
        </li>
        <li class="{{ isActiveMatch('back/hashtags/stages') }}">
            <a href="{{ route('back.hashtags.stages.index') }}">Этапы</a>
        </li>
        <li class="{{ isActiveMatch('back/hashtags/points') }}">
            <a href="{{ route('back.hashtags.points.index') }}">Баллы</a>
        </li>
    </ul>
</li>
