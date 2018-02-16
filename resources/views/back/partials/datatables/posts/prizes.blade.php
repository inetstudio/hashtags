<ul class="prizes-list m-t small-list">
    @foreach ($item['prizes'] as $prize)
        @php
            if ($prize->pivot->stage_id) {
                $type = 'stage';
            } elseif ($prize->pivot->date) {
                $type = 'day';
            } else {
                $type = 'winner';
            }

            switch ($type) {
                case 'stage':
                    $prizeTitle = \InetStudio\Hashtags\Models\StageModel::find($prize->pivot->stage_id)->name.' / ';
                    break;
                case 'day':
                    $prizeTitle = \Carbon\Carbon::createFromFormat('Y-m-d', $prize->pivot->date)->format('d.m.Y').' / ';
                    break;
                case 'winner':
                    $prizeTitle = '';
                    break;
            }
        @endphp
        <li>
            <span class="m-l-xs">{{ $prizeTitle }}{{ $prize->name }}</span>
        </li>
    @endforeach
</ul>