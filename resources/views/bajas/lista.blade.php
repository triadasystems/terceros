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
                    <!-- <div class="form-group row">
                        <div class="col-lg-12 text-right">
                            <button type="button" class="btn btn-warning" id="regresar">{{ __('Regresar') }}</button>
                            <button class="btn btn-success" id="registerObj">Alta de Conexiones</button>
                        </div>
                    </div> -->
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
    // $(document).on("click", "#edit", function () {
    //     var id = $(this).attr("data-id");
    //     window.location.href = 'edit/' + id;
    // });
    // $(document).on("click", "#consultas", function () {
    //     var id = $(this).attr("data-id");
    //     window.location.href = 'consultas/lista/' + id;
    // });
    // $(document).on("click", "#delact", function(){
    //     var id = $(this).attr("data-id");
    //     var tipo = $(this).attr("data-tipo");

    //     swal({
    //         title: '¿Esta seguro?',
    //         text: "¡Siempre podrá revertir la acción!",
    //         type: 'warning',
    //         showCancelButton: true,
    //         confirmButtonColor: '#3085d6',
    //         cancelButtonColor: '#d33',
    //         confirmButtonText: 'Aceptar'
    //     }).then((result) => {
    //         if (result.value) {
    //             $.ajaxSetup({
    //                 headers: {
    //                     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //                 }
    //             });
    //             $.ajax({
    //                 type: 'POST',
    //                 data: {id: id, tipo: tipo},
    //                 url: ' route("desactivarconexiones") ',
    //                 async: false,
    //                 beforeSend: function(){
    //                     // $("#loading_pes").removeClass("loading_pes_hide");
    //                     mostrarLoadingMail();
    //                 },
    //                 complete: function(){
    //                     // $("#loading_pes").addClass("loading_pes_hide");
    //                     ocultarLoadingMail();
    //                 }
    //             }).done(function(response){
    //                 if(response == "true") {
    //                     table.ajax.reload();
    //                     swal(
    //                         'Activar/Desactivar',
    //                         'La operación se ha realizado con éxito',
    //                         'success'
    //                     )
    //                 } else if(response == "false") {
    //                     swal(
    //                         'Error',
    //                         'La operación no pudo ser realizada',
    //                         'error'
    //                     )
    //                 } else if(response == "middleUpgrade") {
    //                         window.location.href = " route('homeajax') ";
    //                 }
    //             });
    //         }
    //     });
    // });

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

    // $('#registerObj').click(function(){
    //     var url = "{{URL::to('/')}}/conexiones";
    //     url = url+"/create";
    //     $( location ).attr("href", url);
    // });

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
            // { data: 'authorizing_name', name: 'authorizing_name' },
            // { data: 'authorizing_number', name: 'authorizing_number' },
            // { data: 'responsible_name', name: 'responsible_name' },
            // { data: 'responsible_number', name: 'responsible_number' },
            {
                targets: -1,
                render: function (data, type, row) {
                    return '<div class="row"><div class="col-lg-12 text-center"><button type="button" title="Baja de tercero" class="btn btn-danger" data-id-tercero="'+row.id+'" id="edit"><i class="fas fa-user-minus"></i></button></div></div>';
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