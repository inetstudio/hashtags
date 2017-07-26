$(document).ready(function(){

    if ($('.select-tag').length > 0) {
        var url = $('.select-tag').attr('data-search');

        var options = {
            serviceUrl: url,
            minLength: 2,
            onSelect: function (suggestion) {
                $('input[name=tag_id]').val(suggestion.data);
            }
        };

        $('.select-tag').autocomplete(options);
    }

    $('.table').on('click', '.submit-post', function() {
        var modal = $(this).attr('data-target'),
            id = $(this).attr('data-id'),
            tagId = $(this).attr('data-tag-id'),
            tagName = $(this).attr('data-tag-name'),
            form = $(modal).find('form');

        if (tagId != '' && tagName != '') {
            form.find('input[name=tag_id]').val(tagId);
            form.find('input[name=tag_name]').val(tagName);
        }

        form.attr('action', form.attr('data-action').replace('_id_', id));
    });

    $('.view-toggle a').on('click', function (event) {
        var mode = $(this).attr('data-target');

        $('.row.view').removeClass('fadeInDown').addClass('animated').addClass('fadeOutUp').removeClass('active');
        $('.row[data-mode='+mode+']').removeClass('fadeOutUp').addClass('active').addClass('fadeInDown');

        $('.row.view').find('img').lazyLoadXT()
    });
});
