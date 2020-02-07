<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreArticulo extends FormRequest
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
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'codprov' => 'nullable|unique:articulos|max:13',
            'codarticulo' => 'required||min:13|max:13unique:articulos',
            'articulo' => 'required|min:1|max:190|unique:articulos',
            'descripcion' => 'required|max:190',
            'precio' => 'required',
            'stockminimo' => 'required',
            'litros' => 'required',
            'marca_id' => 'nullable',
            'categoria_id' => 'nullable',
            'foto' => 'nullable'
        ];
    }
}
