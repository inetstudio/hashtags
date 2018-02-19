<?php

namespace InetStudio\Hashtags\Http\Requests\Back\Tags;

use Illuminate\Foundation\Http\FormRequest;
use InetStudio\Hashtags\Contracts\Http\Requests\Back\Tags\SaveTagRequestContract;

/**
 * Class SaveTagRequest.
 */
class SaveTagRequest extends FormRequest implements SaveTagRequestContract
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
            'name.required' => 'Поле «Название» обязательно для заполнения',
            'name.max' => 'Поле «Название» не должно превышать 255 символов',
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
            'name' => 'required|max:255',
        ];
    }
}
