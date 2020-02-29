<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSupplier extends FormRequest
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
            'razonsocial' => 'required|min:1|max:190|unique:suppliers,razonsocial,' . $this->supplier,
            'cuit' => 'required|min:11|max:11|unique:suppliers,cuit,' . $this->supplier,
            'direccion' => 'required|min:1|max:190',
            'telefono' => 'required|min:6|max:13',
            'email' => 'required|email',
            'codigopostal' => 'required|min:4|max:4',
            'localidad' => 'required|min:1|max:190',
            'provincia' => 'required|min:1|max:190',
            'observaciones' => 'nullable'
        ];
    }
}
