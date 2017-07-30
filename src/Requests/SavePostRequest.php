<?php

namespace InetStudio\Hashtags\Requests;

use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;

class SavePostRequest extends FormRequest
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
            'status_id.required' => 'Поле «Статус» обязательно для заполнения',
            'status_id.integer' => 'Поле «Статус» должно быть числом',
            'status_id.exists' => 'Такое значение поля «Статус» не существует',
            'stage_id.integer' => 'Поле «Этап» должно быть числом',
            'stage_id.exists' => 'Такое значение поля «Этап» не существует',
            'prize_id.integer' => 'Поле «Приз» должно быть числом',
            'prize_id.exists' => 'Такое значение поля «Приз» не существует',
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
            'status_id' => 'required|integer|exists:contest_by_city_tag_statuses,id',
            'stage_id' => 'nullable|integer|exists:hashtags_stages,id',
            'prize_id' => 'nullable|integer|exists:hashtags_prizes,id',
        ];
    }
}
