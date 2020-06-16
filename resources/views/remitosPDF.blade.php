<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <style>
        body {
            font-family: 'Calibri', sans-serif;
        }

        h1 {
            font-size: 30px;
        }

        h2 {
            font-size: 25px;
        }

        h3 {
            font-size: 20px;
        }

        h4 {
            font-size: 15px;
        }

        .center {
            text-align: center;
        }

        table {
            display: table;
            border-collapse: collapse;
            width: 100%;
        }

        tr {
            display: table-row;
        }

        td {
            display: table-column;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th {
            text-align: center;
            font-size: 10px;
            padding: 2px;
            background-color: #aaaaaa;
        }

        tr.body th {
            text-align: center;
            font-size: 10px;
            padding: 2px;
            background-color: #ffffff;
        }

        .container {
            margin: 0;
            padding: 0;
        }

        .pre-header {
            width: 100%;
            height: 10mm;
            border: 1px solid black;
            text-align: center;
            line-height: 10px;
        }

        .header {
            width: 100%;
            height: 45mm;
            display: block;
        }

        .header-left {
            width: 50%;
            height: 45mm;
            border: solid 1px black;
            border-top: none;
            border-right: none;
            float: left;
            margin-top: -20px;
        }

        .header-left p {
            line-height: 5px;
            font-size: 12px;
            margin-left: 20px;
        }

        .header-left h2 {
            margin-bottom: 40px;
        }

        .header-right {
            width: 50%;
            height: 45mm;
            border: solid 1px black;
            border-left: none;
            border-top: none;
            float: right;
            margin-top: -20px;
        }

        .header-right p {
            line-height: 5px;
            font-size: 12px;
            text-align: right;
            margin-right: 20px;
        }

        .header-middle {
            width: 70px;
            height: 70px;
            border: 1px solid black;
            background-color: white;
            border-top: none;
            position: absolute;
            margin-left: 45%;
            text-align: center;
            line-height: 5px;
        }

        .header-middle h1 {
            font-size: 45px;
            margin-top: 45px;
        }

        .type {
            margin: 0;
        }

        .v-divider {
            width: 0px;
            height: 100px;
            border: 1px solid black;
            position: absolute;
            margin-left: 50%;
            margin-top: 70px;
        }

        .pre-body {
            width: 100%;
            height: 45mm;
            border: solid 1px black;
            border-top: none;
            margin-top: -40px;
        }

        .pre-body p {
            line-height: 8px;
            margin-left: 20px;
        }

        .footer {
            position: absolute;
            bottom: 0;
        }

        .footer .details {
            border: solid 1px black;
            width: 100%;
            line-height: 8px;
            padding-top: 15px;
            padding-right: 15px;
            text-align: right;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="pre-header">
            <h2><b>ORIGINAL</b></h2>
        </div>
        <div class="header">
            <br>
            <div class="header-middle">
                <div class="type">
                    <h1><b>X</b></h1>
                </div>
            </div>
            <div class="header-left">
                <h2 class="center">{{$configuracion['nombrefantasia']}}</h2>
                <p><b>Razón Social: </b>{{$configuracion['razonsocial']}}</p>
                <p><b>Domicilio Comercial: </b>{{$configuracion['domiciliocomercial']}}</p>
                <p><b>Condición Frente al IVA: </b>{{$configuracion['condicioniva']}}</p>
            </div>
            <div class="v-divider"></div>
            <div class="header-right">
                <br>
                <p><b>Punto de Venta: </b>0000{{$configuracion['puntoventa']}}<b> Comprobante Nº: </b>{{$factura->numventa}}</p>
                <p><b>Comprobante Adherido: </b>{{$factura->comprobanteadherido}}</p>
                <p><b>Fecha de Emisión: </b>{{$factura->fecha}}</p>
                <p><b>Cuit: </b>{{$configuracion['cuit']}}</p>
                <p><b>Ingresos Brutos: </b>{{$configuracion['cuit']}}</p>
                <p><b>Inicio de Actividades: </b>{{$configuracion['inicioactividades']}}</p>
            </div>
        </div>
        <br><br>
        <div class="pre-body">
            <br>
            <p><b>CUIT: </b>{{$cliente->documentounico}}</p>
            <p><b>Razón Social: </b>{{$cliente->razonsocial}}</p>
            <p><b>Condición Frente al IVA: </b>{{$cliente->condicioniva}}</p>
            <p><b>Domicilio: </b>{{$cliente->direccion}}</p>
            <p><b>Condición de Venta: </b>{{$factura->condicionventa}}</p>
        </div>
        <br>
        <div class="body">
            <table>
                <tr>
                    <th>Cod. Producto</th>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>U. Medida</th>
                </tr>
                @foreach($detalles as $detalle)
                <tr class="body">
                    <th>{{$detalle->codarticulo}}</th>
                    <th>{{$detalle->articulo}}</th>
                    <th>{{$detalle->cantidad}}</th>
                    <th>{{$detalle->medida}}</th>
                </tr>
                @endforeach
            </table>
        </div>
        <br>
        <div class="footer">
            @if($factura->observaciones)
            <p><b>Observaciones: </b>{{$factura->observaciones}}</p>
            @endif
            <br>

            Firma................................................................. Aclaración................................................................

        </div>
    </div>

    <div class="page-break"></div>

    <div class="container">
        <div class="pre-header">
            <h2><b>DUPLICADO</b></h2>
        </div>
        <div class="header">
            <br>
            <div class="header-middle">
                <div class="type">
                    <h1><b>X</b></h1>
                </div>
            </div>
            <div class="header-left">
                <h2 class="center">{{$configuracion['nombrefantasia']}}</h2>
                <p><b>Razón Social: </b>{{$configuracion['razonsocial']}}</p>
                <p><b>Domicilio Comercial: </b>{{$configuracion['domiciliocomercial']}}</p>
                <p><b>Condición Frente al IVA: </b>{{$configuracion['condicioniva']}}</p>
            </div>
            <div class="v-divider"></div>
            <div class="header-right">
                <br>
                <p><b>Punto de Venta: </b>0000{{$configuracion['puntoventa']}}<b> Comprobante Nº: </b>{{$factura->numventa}}</p>
                <p><b>Comprobante Adherido: </b>{{$factura->comprobanteadherido}}</p>
                <p><b>Fecha de Emisión: </b>{{$factura->fecha}}</p>
                <p><b>Cuit: </b>{{$configuracion['cuit']}}</p>
                <p><b>Ingresos Brutos: </b>{{$configuracion['cuit']}}</p>
                <p><b>Inicio de Actividades: </b>{{$configuracion['inicioactividades']}}</p>
            </div>
        </div>
        <br><br>
        <div class="pre-body">
            <br>
            <p><b>CUIT: </b>{{$cliente->documentounico}}</p>
            <p><b>Razón Social: </b>{{$cliente->razonsocial}}</p>
            <p><b>Condición Frente al IVA: </b>{{$cliente->condicioniva}}</p>
            <p><b>Domicilio: </b>{{$cliente->direccion}}</p>
            <p><b>Condición de Venta: </b>{{$factura->condicionventa}}</p>
        </div>
        <br>
        <div class="body">
            <table>
                <tr>
                    <th>Cod. Producto</th>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>U. Medida</th>
                </tr>
                @foreach($detalles as $detalle)
                <tr class="body">
                    <th>{{$detalle->codarticulo}}</th>
                    <th>{{$detalle->articulo}}</th>
                    <th>{{$detalle->cantidad}}</th>
                    <th>{{$detalle->medida}}</th>
                </tr>
                @endforeach
            </table>
        </div>
        <br>
        <div class="footer">
            @if($factura->observaciones)
            <p><b>Observaciones: </b>{{$factura->observaciones}}</p>
            @endif
            <br>

            Firma................................................................. Aclaración................................................................

        </div>
    </div>

</body>

</html>
