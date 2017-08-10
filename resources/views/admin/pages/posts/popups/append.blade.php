<div class="modal inmodal fade" id="add" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Закрыть</span></button>
                <h4 class="modal-title">Добавление поста</h4>
            </div>
            <form method="post" action="{{ route('back.hashtags.posts.append') }}" class="form-horizontal">
                {{ csrf_field() }}
                <div class="modal-body">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Социальная сеть</label>
                        <div class="col-sm-10">
                            <select class="form-control m-b" name="social_network">
                                <option>Instagram</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Ссылка на пост</label>
                        <div class="col-sm-10">
                            <input type="text" name="post_link" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">Отмена</button>
                    <button class="btn btn-primary" type="submit">Добавить</button>
                </div>
            </form>
        </div>
    </div>
</div>