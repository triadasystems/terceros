var $totalReg = 0;  
var $consecutivo = "";
var $grupos = 0;
var $status = "";
var $operacion = "";

var $ejecutables = 0;
var $ejecutablesad = 0;
var $recorrido = 0;
var $pasa = 0;
var $zero_bueno = 0;
var $limpia = 0;

var $barProgress = 0;
var $pasoBarra = 0;

function totalRegistros() {
    var a = $("#tipo-au-ba").val();

    $.ajax({
        type: 'GET',
        url: 'obtenerTotalConexiones/'+a,
        async: false
    }).done(function(response){
        if(response == "middleExecution") {
            ocultarLoading();
            window.location.href = "../homeajax";
        } else {
            sessionStorage.desactivaLogout = 0;
            $totalReg = response;
        }
    });
}

function consecutivo() {
    var a = $("#tipo-au-ba").val();
    
    $.ajax({
        type: 'GET',
        url: 'obtenerConsecutivo',
        async: false
    }).done(function(response){
        $consecutivo = response;
    });       
}

function cargaBarra() {
    $(".txt-bar-progress").css("width", $barProgress+"%");
    $(".txt-bar-progress").text(Math.round($barProgress)+"%");
}

function descargaBarra() {
    $(".txt-bar-progress").css("width", "0%");
    $(".txt-bar-progress").text("0%");
}
function setLog()
{
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        type: 'POST',
        url:  'log',
        data: {datos:$status,oper:$operacion,con:$consecutivo},
        async: true
    }).done(function(response)
    {
    });
}
function ejecutar(cant, total) {
    var a = $("#tipo-au-ba").val();
     $operacion= a;
     $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        type: 'POST',
        //url: 'ejecutarMigracion/'+a,
        url: 'ejecutarMigracion',
        data: {ejecutables: $ejecutables, ejecutablesad: $ejecutablesad, recorrido: $recorrido, a: a, pasa: $pasa, zero_bueno: $zero_bueno, limpia: $limpia, cant: cant, total: total, consecutivo: $consecutivo, status: $status},
        async: true,
        dataType: 'json'
    }).done(function(response){
        $.each(response, function( index, value ) {  
            switch(index) {
                case "ejecutables":
                    $ejecutables = value;
                    if($ejecutables == 1 && $ejecutablesad == 0 && $recorrido == 0 && $pasa == 0 && $zero_bueno == 0 && $limpia == 0) {
                        if($pasoBarra < 1) {
                            $barProgress += 16.66666666666667;
                        }
                        cargaBarra();
                        $pasoBarra = 1;
                    }
                    break;
                case "ejecutablesad":
                    $ejecutablesad = value;
                    if($ejecutables == 1 && $ejecutablesad == 1 && $recorrido == 0 && $pasa == 0 && $zero_bueno == 0 && $limpia == 0) {
                        if($pasoBarra < 2) {
                            $barProgress += 16.66666666666667;
                        }
                        cargaBarra();
                        $pasoBarra = 2;
                    }
                    break;
                case "recorrido":
                    $recorrido = value;
                    if($ejecutables == 1 && $ejecutablesad == 1 && $recorrido == 1 && $pasa == 0 && $zero_bueno == 0 && $limpia == 0) {
                        if($pasoBarra < 3) {
                            $barProgress += 16.66666666666667;
                        }
                        cargaBarra();
                        $pasoBarra = 3;
                    }
                    break;
                case "pasa":
                    $pasa = value;
                    if($ejecutables == 1 && $ejecutablesad == 1 && $recorrido == 1 && $pasa == 1 && $zero_bueno == 0 && $limpia == 0) {
                        if($pasoBarra < 4) {
                            $barProgress += 16.66666666666667;
                        }
                        cargaBarra();
                        $pasoBarra = 4;
                    }
                    break;
                case "zero_bueno":
                    $zero_bueno = value;
                    if($ejecutables == 1 && $ejecutablesad == 1 && $recorrido == 1 && $pasa == 1 && $zero_bueno == 1 && $limpia == 0) {
                        if($pasoBarra < 5) {
                            $barProgress += 16.66666666666667;
                        }
                        cargaBarra();
                        $pasoBarra = 5;
                    }
                    break;
                case "limpia":
                    $limpia = value;
                    if($ejecutables == 1 && $ejecutablesad == 1 && $recorrido == 1 && $pasa == 1 && $zero_bueno == 1 && $limpia == 1) {
                        if($pasoBarra < 6) {
                            $barProgress += 16.66666666666667;
                        }
                        cargaBarra();
                        $pasoBarra = 6;
                    }
                    break;
                case "cant":
                    $grupos = value;
                    break;
                case "status":
                    $status = value;
                    break;
            }
        });
        
        if(
            $ejecutables == 0 ||
            $ejecutablesad == 0 ||
            $recorrido == 0 ||
            $pasa == 0 ||
            $zero_bueno == 0 ||
            $limpia == 0
        ) {
            ejecutar($grupos, $totalReg, $status);
        } else {
            $ejecutables = 0;
            $ejecutablesad = 0;
            $recorrido = 0;
            $pasa = 0;
            $zero_bueno = 0;
            $limpia = 0;
            $totalReg = 0;
            $barProgress = 0;
            $pasoBarra = 0;

            descargaBarra();

            var resultErrors;
            var resultOk;
            var resultado = '';

            resultErrors = $status["errores"];
            resultOk = $status["ok"];
            
            $.each(resultOk, function( index, value ) {
                $.each(value, function( ind, val ) {
                    if (ind != 'app_id')
                    {
                        resultado += '<tr><td>'+ind+'</td><td>'+val+'</td></tr>';
                    }
                });
            });
            $.each(resultErrors, function( index, value ) {
                $.each(value, function( ind, val ) {
                    if (ind != 'app_id')
                    {
                        resultado += '<tr><td>'+ind+'</td><td>'+val+'</td></tr>';
                    }
                });
            });
            tableBajas.ajax.reload();
            tableAuto.ajax.reload();
            setLog($status,$operacion, $consecutivo);
            ocultarLoading();
            swal({
                title: '<strong>Resultado de la ejecución</strong>',
                type: 'info',
                html: '<div style="overflow-y:auto;height: 350px;"><table border="1" style="width:100%;"><thead><tr><th>Conexión</th><th>Resultado</th></tr></thead><tbody>'+resultado+'</tbody></table></div>',
                showCloseButton: false,
                showCancelButton: false,
                focusConfirm: false,
                confirmButtonText: 'Confirmar',
                allowOutsideClick: false,
            });
            $status = '';
            $grupos = 0;
            sessionStorage.desactivaLogout = 1;
        }
    }).fail( function( jqXHR, textStatus, errorThrown ) {
        if(jqXHR.responseJSON !== undefined && jqXHR.responseJSON.message == "Unauthenticated.") {
            ocultarLoading();
            swal({
                title: 'Advertencia',
                text: "Su usuario ha sido usado nuevamente, por lo que será desconectado del aplicativo",
                type: 'warning',
                showCancelButton: false,
                confirmButtonColor: '#3085d6',
                allowOutsideClick: false,
                allowEscapeKey: false,
                confirmButtonText: 'De acuerdo'
            }).then((result) => {
                if (result.value) {
                    var url_login = $("#url_login").val();
                    window.location.href = url_login;
                }
            })
        }
    });
}
$(function() {
    $('#btnEjecutar').on('click', function() {
        mostrarLoading();
        totalRegistros();
        consecutivo();

        setTimeout(function(){ ejecutar($grupos, $totalReg, $status); }, 1000);
        
    });
}); 