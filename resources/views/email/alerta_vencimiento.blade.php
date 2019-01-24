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
                <th><p><b>Número del empleado</b></p></th>
                <th><p><b>Nombre completo del empleado</b></p></th>
                <th><p><b>Gafete</b></p></th>
                <th><p><b>Autorizador</b></p></th>
                <th><p><b>Responsable</b></p></th>
                <th><p><b>Empresa</b></p></th>
                <th><p><b>Días para baja</b></p></th>
            </thead>
            <tbody>
                @foreach($obj_mail->data as $val)
                <tr>
                    <td align="center">{{ $val["emp_keyemp"]}}</td>
                    <td align="center">{{ $val["full_name"] }}</td>
                    <td align="center">{{ $val["gafete"] }}</td>
                    <td align="center">{{ $val["autorizador"] }}</td>
                    <td align="center">{{ $val["responsable"] }}</td>
                    <td align="center">{{ $val["empresa"] }}</td>
                    <td align="center">{{ $val["d_dif"] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>