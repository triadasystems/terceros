@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="panel-pbc">
                <div class="panel-pbc-head">
                    Lista de Terceros Asignados
                </div>
                <div class="panel-pbc-body">
                    <div class="row table-responsive">
                        <div class="col-lg-12 table-responsive">
                            <table class="table table-bordered" id="subordinados-table">
                                <thead>
                                    <tr>
                                        <th>Nombre del Tercero</th>
                                        <th># Gafete del Tercero</th>
                                        <th>E-mail del Tercero</th>
                                        <th>Autorizador #</th>
                                        <!-- <th># Autorizador</th> -->
                                        <th>Responsable #</th>
                                        <!-- <th># Responsable</th> -->
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                            </table>  
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    $(document).ready(function(){
        $(document).on("click", "#bajaAcc", function () {
            var id = $(this).attr("data-id-tercero");
            var dateInitial = $(this).attr("data-date-initial");
            var dateRealLow = $(this).attr("data-real-low-date");
            var dateLow = $(this).attr("data-date-low");
            var status = $(this).attr("data-estatus");

            switch (status) {
                case "1":
                    status = 'Activo';
                    break;
                case "2":
                    status = 'Baja';
                    break;
                case "3":
                    status = 'Bloqueado';
                    break;
            }

            Swal({
                title: 'BAJA DE USUARIO DE TERCERO',
                // type: 'info',
                html:
                '<div class="container" style="margin-top: 10px;">'+
                    '<form method="post" action="">'+
                        '<input type="hidden" name="id_tercero" id="id_tercero" value="'+id+'" />'+
                        '<div class="form-group row">'+
                            '<div class="col-md-6">'+
                                '<label for="lbl-solicitante" class="col-lg-12 col-form-label text-left txt-bold">Solicitante</label>'+
                                '<label for="solicitante" class="col-lg-12 col-form-label text-left">{{ Auth::user()->name }}</label>'+
                            '</div>'+
                            '<div class="col-md-6">'+
                                '<label for="lbl-estado" class="col-lg-12 col-form-label text-left txt-bold">Estado Actual</label>'+
                                '<label for="estado" class="col-lg-12 col-form-label text-left">'+status+'</label>'+
                            '</div>'+
                        '</div>'+
                        '<div class="form-group row">'+
                            '<div class="col-md-6">'+
                                '<label for="fecha_inicial" class="col-lg-12 col-form-label text-left txt-bold">Fecha Inicial Registrada</label>'+
                                '<input id="fecha_inicial" type="text" readonly class="form-control" name="fecha_inicial" value="'+dateInitial+'" required autofocus>'+
                            '</div>'+
                            '<div class="col-md-6">'+
                                '<label for="fecha_baja" class="col-lg-12 col-form-label text-left txt-bold">Fecha de Baja Registrada</label>'+
                                '<input id="fecha_baja" type="text" readonly class="form-control" name="fecha_baja" value="'+dateLow+'" required autofocus>'+
                            '</div>'+
                        '</div>'+
                        '<div class="form-group row">'+
                            '<div class="col-md-12">'+
                                '<label for="motivo" class="col-lg-12 col-form-label text-left txt-bold">Motivo de Baja</label>'+
                                '<select id="motivo" class="form-control" name="motivo" required>'+
                                '<option value="">Seleccione...</option>'+
                                @foreach($tiposBajas as $tB)
                                    '<option value="{{ $tB['id'] }}">{{ $tB['code'] }} | {{ $tB['type'] }}</option>'+
                                @endforeach
                                '</select>'+
                                '<span id="motivo_baja" class="error-msj" role="alert">'+
                                    '<strong>El campo Motivo de Baja es obligatorio</strong>'+
                                '</span>'+
                            '</div>'+
                        '</div>'+
                        '<div class="form-group row">'+
                            '<div class="col-md-12">'+
                                '<label for="real_low_date" class="col-lg-12 col-form-label text-left txt-bold">Fecha Real de Baja (Opcional)</label>'+
                                '<input id="real_low_date" type="date" class="form-control" name="real_low_date" autofocus>'+

                                '<span id="errmsj_reallowdate" class="error-msj" role="alert">'+
                                    '<strong>El campo Fecha Real es obligatorio</strong>'+
                                '</span>'+
                            '</div>'+
                        '</div>'+
                        '<div class="col-md-12 text-right">'+
                            '<label class="col-lg-12 col-form-label">&nbsp;</label>'+
                            '<a href="#" onclick="swal.closeModal(); return false;" class="btn btn-danger">Cancelar</a>&nbsp;&nbsp;'+
                            '<input class="btn btn-primary" id="ap_baja" type="button" value="Aplicar Baja">'+
                        '</div>'+
                    '</form>'+
                '</div>',
                showCloseButton: true,
                showCancelButton: false,
                showConfirmButton: false,
                focusConfirm: false,
                confirmButtonText: 'Aplicar Baja',
                confirmButtonAriaLabel: 'Aplicar Baja',
                cancelButtonText: 'Cancelar Baja',
                allowOutsideClick: false,
            });
        });
        
        $(document).on("click", "#ap_baja", function () {
            var motivo = $("#motivo").val();
            var fechaRealBaja = $("#real_low_date").val();
            var badgeN = $(this).attr("data-badge-number");
            var email = $(this).attr("data-email");

            if (motivo == null || motivo == "") {
                mostrarError("motivo_baja");
            } else {
                ocultarError("motivo_baja");
                
                var idTercero = $("#id_tercero").val();

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    type: 'POST',
                    data: { id: idTercero, motivo: motivo, real_low_date: fechaRealBaja, email: email, badge_number: badgeN },
                    dataType: 'JSON',
                    url: '{{ route("bajatercero") }}',
                    async: false,
                    beforeSend: function(){
                        console.log("Cargando");
                    },
                    complete: function(){
                        console.log("Listo");
                    }
                }).done(function(response){
                    if(response === true) {
                        table.ajax.reload();
                        swal(
                            'Bajas Aplicada',
                            'La operación se ha realizado con éxito',
                            'success'
                        )
                    } else if(response === false) {
                        swal(
                            'Error',
                            'La operación no pudo ser realizada',
                            'error'
                        )
                    } else if(response == "middleUpgrade") {
                            window.location.href = " route('homeajax') ";
                    }
                }).fail(function(response){
                    console.log(response.responseJSON.errors);
                    if (response.responseJSON !== undefined && response.responseJSON.errors != undefined && response.responseJSON.errors.motivo != undefined && response.responseJSON.errors.motivo[0] != "") {
                        mostrarError("motivo_baja");
                    }
                });
            }
        });

        // @if(session('confirmacion'))
        //     swal(
        //         'Conexión agregada',
        //         'La operación se ha realizado con éxito',
        //         'success'
        //     )
        // @endif
        // @if(session('actualizado'))
        //     swal(
        //         'Conexión editada',
        //         'La operación se ha realizado con éxito',
        //         'success'
        //     )
        // @endif
        // @if(session('errorMsj'))
        //     swal(
        //         'Error',
        //         '{{ session("errorMsj") }}',
        //         'error'
        //     )
        // @endif

        var table = $('#subordinados-table').DataTable({
            language: {
                url: "{{ asset('json/Spanish.json') }}",
                buttons: {
                    copyTitle: 'Tabla copiada',
                    copySuccess: {
                        _: '%d líneas copiadas',
                        1: '1 línea copiada'
                    }
                }
                // url: "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
            },
            processing: true,
            serverSide: true,
            
            ajax: '{!! route("tercerosAsignados.data", ["noEmployee" => Auth::user()->noEmployee]) !!}',
            columns: [
                {
                    // targets: 0,
                    render: function (data, type, row) {
                        var nombreC = row.name+' '+row.lastname1+' '+row.lastname2;
                        return nombreC;
                    }
                },
                { data: 'badge_number', name: 'badge_number' },
                { data: 'email', name: 'email' },
                {
                    render: function (data, type, row) {
                        var autorizador = row.authorizing_name+' | '+row.authorizing_number;
                        return autorizador;
                    }
                },
                {
                    render: function (data, type, row) {
                        var reponsable = row.responsible_name+' | '+row.responsible_number;
                        return reponsable;
                    }
                },
                {
                    targets: -1,
                    render: function (data, type, row) {
                        return '<div class="row"><div class="col-lg-12 text-center"><button type="button" title="Baja de tercero" class="btn btn-danger" data-date-initial="'+row.initial_date+'" data-date-low="'+row.low_date+'" data-id-tercero="'+row.id+'" data-estatus="'+row.status+'" data-email="'+row.email+'" data-badge-number="'+row.badge_number+'" id="bajaAcc"><i class="fas fa-user-minus"></i></button></div></div>';
                    }
                }
            ],
            dom: 'Blfrtip',
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todo"]],
            buttons: [
                {
                    extend: 'copyHtml5',
                    text: 'Copiar',
                },
                'excelHtml5',
                'csvHtml5',
                'pdfHtml5'
            ]
        });
    });
</script>
@endpush