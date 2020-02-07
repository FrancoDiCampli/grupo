<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFactura extends FormRequest
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
            'ptoventa' => 'required|max:5',
            'codcomprobante' => 'required|max:2',
            'letracomprobante' => 'required|max:2',
            'numfactura' => 'required|max:8',
            'cuit' => 'min:0|max:13',
            'fecha' => 'required',
            'bonificacion' => 'required',
            'recargo' => 'required',
            'subtotal' => 'required',
            'total' => 'required',
            'pagada' => 'required',
            'condicionventa' => 'required',
            'comprobanteafip' => 'nullable',
            'cae' => 'nullable',
            'fechavto' => 'nullable',
            'codbarra' => 'nullable',
            'cliente_id' => 'required',
            'user_id' => 'required',
            'compago' => 'nullable'
        ];
    }
}
