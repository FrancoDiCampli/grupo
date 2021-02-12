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
                <h2 class="center">Controller</h2>
                <p><b>Razón Social: </b>Controller Programación y Diseño</p>
                <p><b>Domicilio Comercial: </b>Donovan 430</p>
            </div>
            <div class="v-divider"></div>
            <div class="header-right">
                <h2 class="center">Presupuesto</h2>
                <p><b>Punto de Venta: </b>00001<b> Comprobante adherido: </b>13</p>
                <p><b>Fecha de Emisión: </b>22/01/2021</p>
            </div>
        </div>
        <br><br>
        <div class="pre-body">
            <br>
            <p><b>CUIT: </b>30716517841</p>
            <p><b>Razón Social: </b>NEA APC SAS</p>
            <p><b>Condición Frente al IVA: </b>RESPONSABLE INSCRIPTO</p>
            <p><b>Domicilio: </b>Villa Ángela</p>
            <p><b>Valido Hasta: </b>22/02/2021</p>
        </div>
        <br>
        <div class="body">
            <table>
                <tr>
                    <th>Servicio</th>
                    <th>Tiempo Des</th>
                    <th>Precio Unit</th>
                    <th>Subtotal</th>
                </tr>
                <tr class="body">
                    <th>Asignar clientes a vendedores.</th>
                    <th>30 días</th>
                    <th>30 U$D</th>
                    <th>30 U$D</th>
                </tr>
                <tr class="body">
                    <th>Añadir acciones en los comprobantes desde la vista de clientes.</th>
                    <th>30 días</th>
                    <th>30 U$D</th>
                    <th>30 U$D</th>
                </tr>
                <tr class="body">
                    <th>Comprobantes por articulos.</th>
                    <th>30 - 45 días</th>
                    <th>70 U$D</th>
                    <th>70 U$D</th>
                </tr>
                <tr class="body">
                    <th>Agregar notas de crédito y debito.</th>
                    <th>45 - 60 días</th>
                    <th>100 U$D</th>
                    <th>100 U$D</th>
                </tr>
                <tr class="body">
                    <th>Añadir referencias a observaciones</th>
                    <th>45 - 60 días</th>
                    <th>100 U$D</th>
                    <th>100 U$D</th>
                </tr>
                <tr class="body">
                    <th>Realizar pagos totales, parciales y por ventas</th>
                    <th>45 - 60 días</th>
                    <th>100 U$D</th>
                    <th>100 U$D</th>
                </tr>
            </table>
        </div>
        <br>
        <div class="footer">
            <p><b>Observaciones: </b>
                <br>
                El precio final es el equivalente en pesos a dólares estadounidenses, tipo de cambio vendedor del banco de la Nación Argentina del día del efectivo pago.
                <br>
                El pago es sobre el total del presupuesto presentado y se puede realizar en un máximo de dos partes.
                <br>
                El pago debe realizarse una vez aprobado el presupuesto, ya sea en su totalidad o en la primera parte del mismo en caso que se desee pagar en dos partes.
                <br>
                El tiempo de desarrollo de los servicios presentados NO es acumulativo, por lo tanto el tiempo de desarrollo sobre el total del presupuesto es de 30 a 60 días.
                <br>
                Si desea conocer con más detalle cada uno de los servicios del presupuesto, puede consultar las tarjetas adjuntas en trello. https://trello.com/b/BVfX2Wil/grupo-apc-servicio-t%C3%A9cnico
            </p>
            <div class="details">
                <p><b>Subtotal: </b>U$D 430</p>
                <p><b>Total: </b>U$D 430</p>
            </div>
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
                <h2 class="center">Controller</h2>
                <p><b>Razón Social: </b>Controller Programación y Diseño</p>
                <p><b>Domicilio Comercial: </b>Donovan 430</p>
            </div>
            <div class="v-divider"></div>
            <div class="header-right">
                <h2 class="center">Presupuesto</h2>
                <p><b>Punto de Venta: </b>00001<b> Comprobante adherido: </b>13</p>
                <p><b>Fecha de Emisión: </b>22/01/2021</p>
            </div>
        </div>
        <br><br>
        <div class="pre-body">
            <br>
            <p><b>CUIT: </b>30716517841</p>
            <p><b>Razón Social: </b>NEA APC SAS</p>
            <p><b>Condición Frente al IVA: </b>RESPONSABLE INSCRIPTO</p>
            <p><b>Domicilio: </b>Villa Ángela</p>
            <p><b>Valido Hasta: </b>22/02/2021</p>
        </div>
        <br>
        <div class="body">
            <table>
                <tr>
                    <th>Servicio</th>
                    <th>Tiempo Des</th>
                    <th>Precio Unit</th>
                    <th>Subtotal</th>
                </tr>
                <tr class="body">
                    <th>Asignar clientes a vendedores.</th>
                    <th>30 días</th>
                    <th>30 U$D</th>
                    <th>30 U$D</th>
                </tr>
                <tr class="body">
                    <th>Añadir acciones en los comprobantes desde la vista de clientes.</th>
                    <th>30 días</th>
                    <th>30 U$D</th>
                    <th>30 U$D</th>
                </tr>
                <tr class="body">
                    <th>Comprobantes por articulos.</th>
                    <th>30 - 45 días</th>
                    <th>70 U$D</th>
                    <th>70 U$D</th>
                </tr>
                <tr class="body">
                    <th>Agregar notas de crédito y debito.</th>
                    <th>45 - 60 días</th>
                    <th>100 U$D</th>
                    <th>100 U$D</th>
                </tr>
                <tr class="body">
                    <th>Añadir referencias a observaciones</th>
                    <th>45 - 60 días</th>
                    <th>100 U$D</th>
                    <th>100 U$D</th>
                </tr>
                <tr class="body">
                    <th>Realizar pagos totales, parciales y por ventas</th>
                    <th>45 - 60 días</th>
                    <th>100 U$D</th>
                    <th>100 U$D</th>
                </tr>
            </table>
        </div>
        <br>
        <div class="footer">
            <p><b>Observaciones: </b>
                <br>
                El precio final es el equivalente en pesos a dólares estadounidenses, tipo de cambio vendedor del banco de la Nación Argentina del día del efectivo pago.
                <br>
                El pago es sobre el total del presupuesto presentado y se puede realizar en un máximo de dos partes.
                <br>
                El pago debe realizarse una vez aprobado el presupuesto, ya sea en su totalidad o en la primera parte del mismo en caso que se desee pagar en dos partes.
                <br>
                El tiempo de desarrollo de los servicios presentados NO es acumulativo, por lo tanto el tiempo de desarrollo sobre el total del presupuesto es de 30 a 60 días.
                <br>
                Si desea conocer con más detalle cada uno de los servicios del presupuesto, puede consultar las tarjetas adjuntas en trello. https://trello.com/b/BVfX2Wil/grupo-apc-servicio-t%C3%A9cnico
            </p>
            <div class="details">
                <p><b>Subtotal: </b>U$D 430</p>
                <p><b>Total: </b>U$D 430</p>
            </div>
        </div>
    </div>

</body>

</html>