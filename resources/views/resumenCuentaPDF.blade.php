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
            height: 40mm;
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
            height: 40mm;
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
            height: 80px;
            border: 1px solid black;
            position: absolute;
            margin-left: 50%;
            margin-top: 70px;
        }

        .pre-body {
            width: 100%;
            height: 35mm;
            border: solid 1px black;
            border-top: none;
            margin-top: -60px;
        }

        .pre-body p {
            line-height: 10px;
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
            <h2><b>RESUMEN DE CUENTA</b></h2>
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
                <p><b>Punto de Venta: </b>0000{{$configuracion['puntoventa']}}<b>
                        <p><b>Fecha de Emisión: </b>{{now()->format('d-m-Y')}}</p>
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
            <p><b>Domicilio: </b>{{$cliente->direccion}}</p>
        </div>
        <p style="text-align: center;">
            <b>Desde: </b> {{$desde}} <b>Hasta: </b> {{$hasta}}
        </p>
        <hr>
        <p style="text-align: center;">
            <b>Saldo Anterior: </b>U$D {{$saldoAnterior}} <b>Saldo: </b>U$D {{$saldo}}
        </p>
        <hr>
        <h3 style="text-align: center;">DEBE (-)</h3>
        <div class="body">
            <table>
                <tr>
                    <th>Fecha</th>
                    <th>Movimiento</th>
                    <th>Debe (-)</th>
                </tr>
                @foreach($cuentas as $cuenta)
                <tr class="body">
                    <th>{{$cuenta->fecha}}</th>
                    <th>{{$cuenta->tipo}}</th>
                    <th>{{$cuenta->importe}}</th>
                </tr>
                @endforeach
            </table>
            <div style="text-align: right;">
                <b>Subtotal (U$D) {{$debe}}</b>
            </div>

        </div>
        <br>
        <h3 style="text-align: center;">HABER (+)</h3>
        <div class="body">
            <table>
                <tr>
                    <th>Fecha</th>
                    <th>Movimiento</th>
                    <th>HABER (+)</th>
                </tr>
                @foreach($pagos as $pago)
                <tr class="body">
                    <th>{{$pago->fecha}}</th>
                    <th>{{$pago->tipo}}</th>
                    <th>{{$pago->importe}}</th>
                </tr>
                @endforeach
            </table>
            <div style="text-align: right;">
                <b>Subtotal (U$D) {{$haber}}</b>
            </div>
        </div>
    </div>

</body>

</html>