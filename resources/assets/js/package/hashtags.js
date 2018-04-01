$(document).ready(function(){
    $('.order-list').each(function () {
        var sortURL = $(this).attr('data-sort-url');
        Sortable.create(this, {
            dataIdAttr: 'data-post-id',
            handle: '.post-drag',
            onUpdate: function (evt) {
                var $itemEl = $(evt.item);

                var data = {
                    currentId: $itemEl.attr('data-post-id'),
                    prev: ($itemEl.next().length > 0) ? $itemEl.next().attr('data-post-id') : 0,
                    next: ($itemEl.prev().length > 0) ? $itemEl.prev().attr('data-post-id') : 0
                };

                $.ajax({
                    'url': sortURL,
                    'type': 'POST',
                    'data': data,
                    'dataType': 'json',
                    'success': function (data) {
                        if (data.success) {
                            toastr.success('', 'Сортировка сохранена', window.window.Admin.options.toastr);
                        } else {
                            toastr.error('', 'При изменении сортировки произошла ошибка', window.window.Admin.options.toastr);
                        }
                    },
                    'error': function () {
                        toastr.error('', 'При изменении сортировки произошла ошибка', window.window.Admin.options.toastr);
                    }
                });
            }
        })
    });

    $('.table').on('click', '.submit-post', function() {
        var modal = $(this).attr('data-target'),
            id = $(this).attr('data-id'),
            tagData = $(this).attr('data-tag'),
            form = $(modal).find('form');

        if (typeof tagData !== 'undefined') {
            var tagDataJSON = JSON.parse(tagData);

            form.find('input[name=tag_data]').val(tagData);
            form.find('input[name=tag]').val(tagDataJSON.title);
        }

        form.attr('action', form.attr('data-action').replace('_id_', id));
    });

    $('.view-toggle a').on('click', function (event) {
        event.preventDefault();

        var mode = $(this).attr('data-target');

        if (mode === 'sort') {
            if ($('[data-src]:not([class*=placeholder])').length > 0) {
                new LazyLoad({
                    elements_selector: '[data-src]:not([class*=placeholder])'
                });
            }

            initImagePlaceholders('#sorting');
        }

        $('.row.view').removeClass('fadeInDown').addClass('animated').addClass('fadeOutUp').removeClass('active');
        $('.row[data-mode='+mode+']').removeClass('fadeOutUp').addClass('active').addClass('fadeInDown');
    });

    $('#moderation table').on('draw.dt', function () {
        if ($('[data-src]:not([class*=placeholder])').length > 0) {
            new LazyLoad({
                elements_selector: '[data-src]:not([class*=placeholder])'
            });
        }

        initImagePlaceholders('table');

        new Clipboard('.clipboard');
    });

    $('.add-prize').on('click', function (event) {
        event.preventDefault();

        $('#add_prize_type').val(null).trigger('change');
        $('#modal_add_prize input[name=date]').val('');
        $('#add_prize_stage').val(null).trigger('change');
        $('#add_prize_prize').val(null).trigger('change');

        $('#modal_add_prize').modal();
    });

    $('#add_prize_type').on('select2:select', function (e) {
        $('.prize-type-fields').slideUp();

        $('.prize-type-fields[data-type='+e.params.data.id+']').slideDown();
    });

    $('#modal_add_prize .save').on('click', function (event) {
        event.preventDefault();

        var type = $('#add_prize_type').select2('data');

        if (type[0].id === '') {
            $('#modal_add_prize').modal('hide');
            return;
        }

        var date = $('#modal_add_prize input[name=date]').val();
        var stageId = $('#add_prize_stage').select2('data');
        var prizeId = $('#add_prize_prize').select2('data');

        var template = $('.prize-item-template li').clone();
        template.find('input[name^=prize_type]').val(type[0].id);

        switch (type[0].id) {
            case 'stage':
                if (prizeId[0].id === '' || stageId[0].id === '') {
                    $('#modal_add_prize').modal('hide');
                    return;
                }

                template.find('span').text(stageId[0].text + ' / ' + prizeId[0].text);
                template.find('input[name^=prize_id]').val(prizeId[0].id);
                template.find('input[name^=stage_id]').val(stageId[0].id);
                template.find('input[name^=date]').val(0);
                break;
            case 'day':
                if (date === '' || prizeId[0].id === '') {
                    $('#modal_add_prize').modal('hide');
                    return;
                }

                template.find('span').text(date + ' / ' + prizeId[0].text);
                template.find('input[name^=prize_id]').val(prizeId[0].id);
                template.find('input[name^=stage_id]').val(0);
                template.find('input[name^=date]').val(date);
                break;
            case 'winner':
                if (prizeId[0].id === '') {
                    $('#modal_add_prize').modal('hide');
                    return;
                }

                template.find('span').text(prizeId[0].text);
                template.find('input[name^=prize_id]').val(prizeId[0].id);
                template.find('input[name^=stage_id]').val(0);
                template.find('input[name^=date]').val(0);
                break;
        }

        template.appendTo('.prizes-list');

        $('#modal_add_prize').modal('hide');
    });

    $('.prizes-list').on('click', '.delete', function (event) {
        event.preventDefault();

        $(this).closest('li').remove();
    });

    function initImagePlaceholders(parent) {
        $(parent).find('img.placeholder').each(function () {
            Holder.run({
                images: $(this).get(0)
            });
        });
    }
});
