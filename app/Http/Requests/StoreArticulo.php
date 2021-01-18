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
            'codprov' => 'nullable|unique:articulos|max:13,codprov,NULL,id,deleted_at,NULL',
            'codarticulo' => 'required||min:13|max:13|unique:articulos,codarticulo,NULL,id,deleted_at,NULL',
            'articulo' => 'required|min:1|max:190|unique:articulos,articulo,NULL,id,deleted_at,NULL',
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
