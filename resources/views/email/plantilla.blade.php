<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Notificación de baja</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" media="screen" href="main.css" />
    <script src="main.js"></script>
</head>
<body>
    <p>Notificación de baja</p>
    <p>El siguiente usuario ha sido dado de baja</p>
    <div>
        <table border="1">
            <thead bgcolor="#fcfcfc">
                <th><p><b>Folio de la solicitud</b></p></th>
                <th><p><b>Número del empleado</b></p></th>
                <th><p><b>Nombre completo del empleado</b></p></th>
                <th><p><b>Empresa</b></p></th>
                <th><p><b>Fecha de baja</b></p></th>
                <th><p><b>Motivo de la baja</b></p></th>
            </thead>
            <tbody>
                @foreach($obj_mail->data as $val)
                <tr>
                    <td align="center">{{ $val["fus"]}}</td>
                    <td align="center">{{ $val["id_externo"] }}</td>
                    <td align="center">{{ $val["nombre"]." ".$val["a_paterno"]." ".$val["a_materno"] }}</td>
                    <td align="center">{{ $val["empresa"] }}</td>
                    <td align="center">{{ $val["fecha_baja"] }}</td>
                    <td align="center">{{ $val["type"] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>