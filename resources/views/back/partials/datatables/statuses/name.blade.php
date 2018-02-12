@php
    $icons = [
        'delete' => 'fa-trash-o',
        'check' => 'fa-question',
        'default' => 'fa-check-square-o',
        'block' => 'fa-minus-circle',
        'main' => 'fa-thumbs-o-up',
    ];

    $iconsHTML = '';
    foreach ($status->classifiers as $classifier) {
        if (isset($icons[$classifier->alias])) {
            $iconsHTML .= '<i class="fa '.$icons[$classifier->alias].'"></i> ';
        }
    }
@endphp

{!! $iconsHTML.$status->name !!}
