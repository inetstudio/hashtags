<?php

namespace InetStudio\Hashtags\Requests;

use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;

class SaveStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Сообщения об ошибках.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'Поле «Название» обязательно для заполнения',
            'name.max' => 'Поле «Название» не должно превышать 255 символов',
            'alias.required' => 'Поле «Алиас» обязательно для заполнения',
            'alias.max' => 'Поле «Алиас» не должно превышать 255 символов',
            'alias.unique' => 'Такое значение поля «Алиас» уже существует',
            'default.unique' => 'Статус по умолчанию уже был выбран',
            'main.unique' => 'Основной статус уже был выбран',
        ];
    }

    /**
     * Правила проверки запроса.
     *
     * @param Request $request
     * @return array
     */
    public function rules(Request $request)
    {
        return [
            'name' => 'required|max:255',
            'alias' => 'required|max:255|unique:hashtags_statuses,alias,'.$request->get('status_id'),
            'default' => 'unique:hashtags_statuses,default,'.$request->get('status_id'),
            'main' => 'unique:hashtags_statuses,main,'.$request->get('status_id'),
        ];
    }
}
