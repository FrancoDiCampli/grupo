<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateArticulo extends FormRequest
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
            'codprov' => 'nullable|min:1|max:13|unique:articulos,codprov,' . $this->id,
            'codarticulo' => 'required|min:13|max:13|unique:articulos,codarticulo,' . $this->id,
            'articulo' => 'required|min:1|max:190|unique:articulos,articulo,' . $this->id,
            'descripcion' => 'required|min:1|max:190',
            'precio' => 'required',
            'stockminimo' => 'required',
            'litros' => 'required',
            'marca_id' => 'nullable',
            'categoria_id' => 'nullable',
            'foto' => 'nullable'
        ];
    }
}
