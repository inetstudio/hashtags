<?php

namespace InetStudio\Hashtags\Http\Requests\Back;

use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class SavePostRequest
 * @package InetStudio\Hashtags\Http\Requests\Back
 */
class SavePostRequest extends FormRequest
{
    /**
     * Определить, авторизован ли пользователь для этого запроса.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Сообщения об ошибках.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'status_id.required' => 'Поле «Статус» обязательно для заполнения',
            'status_id.integer' => 'Поле «Статус» должно быть числом',
            'status_id.exists' => 'Такое значение поля «Статус» не существует',
        ];
    }

    /**
     * Правила проверки запроса.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'status_id' => 'required|integer|exists:hashtags_statuses,id',
        ];
    }
}
