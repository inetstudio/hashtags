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
            'prize_id.integer' => 'Поле «Приз» должно быть числом',
            'prize_id.exists' => 'Такое значение поля «Приз» не существует',
            'prize_id.required_with' => 'Поле «Приз» обязательно для заполнения, если заполнено поле «Дата»',
            'prize_date.date_format' => 'Поле «Дата» должно быть в формате гггг-мм-дд',
            'prize_date.unique' => 'Победитель на данное число уже назначен',
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
            'prize_id' => 'required_with:prize_date|nullable|integer|exists:contest_by_city_tag_prizes,id',
            //'prize_date' => 'nullable|date_format:Y-m-d|unique:contest_by_city_tag_posts,prize_date,'.$request->get('post_id'),
        ];
    }
}
