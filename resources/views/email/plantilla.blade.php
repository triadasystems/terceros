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
                <tr>
                    <td align="center">{{ $obj_mail[0]->fus }}</td>
                    <td align="center">{{ $obj_mail[0]->id_externo }}</td>
                    <td align="center">{{ $obj_mail[0]->nombre." ".$obj_mail[0]->a_paterno." ".$obj_mail[0]->a_materno }}</td>
                    <td align="center">{{ $obj_mail[0]->empresa }}</td>
                    <td align="center">{{ $obj_mail[0]->fecha_baja }}</td>
                    <td align="center">{{ $obj_mail[0]->type }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>