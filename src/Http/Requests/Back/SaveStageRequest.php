<?php

namespace InetStudio\Hashtags\Http\Requests\Back;

use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class SaveStageRequest
 * @package InetStudio\Hashtags\Http\Requests\Back
 */
class SaveStageRequest extends FormRequest
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
            'alias.required' => 'Поле «Алиас» обязательно для заполнения',
            'alias.max' => 'Поле «Алиас» не должно превышать 255 символов',
            'alias.unique' => 'Такое значение поля «Алиас» уже существует',
        ];
    }

    /**
     * Правила проверки запроса.
     *
     * @param Request $request
     *
     * @return array
     */
    public function rules(Request $request): array
    {
        return [
            'name' => 'required|max:255',
            'alias' => 'required|max:255|unique:hashtags_stages,alias,'.$request->get('stage_id'),
        ];
    }
}
