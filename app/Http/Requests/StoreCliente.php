<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCliente extends FormRequest
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
            'razonsocial' => 'required|min:1|max:190',
            'documentounico' => 'required|min:8|max:11|unique:clientes,documentounico,NULL,id,deleted_at,NULL',
            'direccion' => 'required|min:1|max:190',
            'email' => 'required|email|unique:clientes,email,NULL,id,deleted_at,NULL',
            'telefono' => 'required|min:6|max:13',
            'codigopostal' => 'required|min:4|max:4',
            'localidad' => 'required|min:1|max:190',
            'provincia' => 'required|min:1|max:190',
            'condicioniva' => 'required|min:1|max:190',
            'observaciones' => 'nullable',
            'foto' => 'nullable'
        ];
    }

    public function messages()
    {
        return [
            'documentounico.unique' => 'El valor del campo CUIT ya está en uso.'
        ];
    }
}
