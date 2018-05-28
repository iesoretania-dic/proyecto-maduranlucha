$(function(){
    "use strict";

    //**********ZONA JQUERY**********//

    $('.form_datetime').datetimepicker({
        language:  'es',
        weekStart: 1,
        todayBtn:  1,
        autoclose: 1,
        todayHighlight: 1,
        startView: 2,
        forceParse: 0,
        showMeridian: 1,

    });

    // jQuery.datetimepicker.setLocale('es');
    //
    // $('.form_datetime').datetimepicker({
    //     timepicker:true,
    //     format:'Y-m-d H:i',
    //     step:15
    // });


    $('#miTabla').DataTable({
        language: {
            "decimal": "",
            "emptyTable": "No hay información",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ Entradas",
            "infoEmpty": "Mostrando 0 to 0 of 0 Entradas",
            "infoFiltered": "(Filtrado de _MAX_ total entradas)",
            "infoPostFix": "",
            "thousands": ",",
            "lengthMenu": "Mostrar _MENU_ Entradas",
            "loadingRecords": "Cargando...",
            "processing": "Procesando...",
            "search": "Buscar:",
            "zeroRecords": "Sin resultados encontrados",
            "paginate": {
                "first": "Primero",
                "last": "Ultimo",
                "next": "Siguiente",
                "previous": "Anterior"
            }
        },
        // scrollY: "80vh",
        // scrollCollapse: true,
        paginate: true,
        ordering: true,
        info: true,
        responsive: true,
        pageLength: 25,
        order: [[0, "asc"]]
    });


    $('#miTablaControlador').DataTable({
        language: {
            "decimal": "",
            "emptyTable": "No hay información",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ Entradas",
            "infoEmpty": "Mostrando 0 to 0 of 0 Entradas",
            "infoFiltered": "(Filtrado de _MAX_ total entradas)",
            "infoPostFix": "",
            "thousands": ",",
            "lengthMenu": "Mostrar _MENU_ Entradas",
            "loadingRecords": "Cargando...",
            "processing": "Procesando...",
            "search": "Buscar:",
            "zeroRecords": "Sin resultados encontrados",
            "paginate": {
                "first": "Primero",
                "last": "Ultimo",
                "next": "Siguiente",
                "previous": "Anterior"
            }
        },
        // scrollY:        "200px",
        // scrollCollapse: true,
        paginate: true,
        ordering: false,
        info: true,
        responsive: false,
        pageLength: 25,
        // order: [[ 0, "asc" ]]
    });


    let mensajeCliente = $('#cambiosModificarCliente');
    let mensajeIncidencia = $('#cambiosIncidencia');
    let mensajeEliminar = $('#cambiosEliminar');

    if (mensajeCliente.html() === 'No'){
        toastr.warning('Cambios no guardados');
    }
    if (mensajeCliente.html() === 'Si'){
        toastr.success('Cambios guardados');
    }
    if (mensajeIncidencia.html() === 'Si'){
        toastr.success('Incidencia añadida');
    }
    if (mensajeEliminar.html() === 'Si'){
        toastr.warning('Eliminado');
    }



    // toastr.info('Page Loaded!');


    /*toast positions
    *
    * toast-top-right
    * toast-top-center
    * toast-top-letf
    * toast-bottom-right
    * toast-bottom-center
    * toast-bottom-letf
    *
    * */

    /* toast tipe alert
    *
    * toastr.info()
    * toastr.warning()
    * toastr.success()
    * toastr.error()
    *
    */
    // let micheck =  $('#checkRouter');
    //
    //
    //     if(micheck.checked)


    // $('#btnBaja').click(function() {
    //     console.log(micheck.checked);
    //
    //     if(micheck.checked === false){
    //         toastr.success('Hola mundo','',{
    //             closeButton: false,
    //             debug: false,
    //             newestOnTop: false,
    //             progressBar: false,
    //             positionClass: "toast-top-center",
    //             preventDuplicates: true,
    //             onclick: null,
    //             showDuration: "100",
    //             hideDuration: "1000",
    //             timeOut: "5000",
    //             extendedTimeOut: "1000",
    //             showEasing: "swing",
    //             hideEasing: "linear",
    //             showMethod: "show",
    //             hideMethod: "hide"
    //         });
    //     }
    // });


    //***********FIN ZONA JQUERY***********//


    //************VALIDACIONES************//

    //Control sobre el boton de finalizar baja


    let miSelect = $('#solucion');
    let comentario = $('#comentarioFinalizar');

    comentario.css("display","none");

    miSelect.change(function () {
        let opcion = this.options[this.selectedIndex].innerHTML;

        if (opcion === "otros") {
            comentario.css("display","block");
        } else {
            comentario.css("display","none");
        }
    });

    //************************************************//

    let checkInstalacion = $('#checkInstalacion');
    let comentarioInstalacion = $('#comentarioInstalacion');

    // comentarioInstalacion.css("display","none");

    checkInstalacion.change(function () {
        if ($(this).is(':checked')) {
            comentarioInstalacion.css("display","block");
        } else {
            comentarioInstalacion.css("display","none");
        }
    });

    //************************************************//
    //DESACTIVAR EL BOTON DE FINALIZAR INSTALACION//

    let btnInstalacion = $('#btnInstalacion');
    btnInstalacion.attr("disabled", true);

    $('#checkRouterInstalacion').change(function () {

        if (!this.checked) {
            btnInstalacion.attr("disabled", true);
        } else {
            btnInstalacion.attr("disabled", false);
        }
    });

    //DESACTIVAR EL BOTON DE FINALIZAR BAJA//

    let btnBaja = $('#btnBaja');
    btnBaja.attr("disabled", true);

    $('#checkRouterBaja').change(function () {

        if (!this.checked) {
            btnBaja.attr("disabled", true);
        } else {
            btnBaja.attr("disabled", false);
        }
    });

    //DESACTIVAR EL BOTON DE CAMBIO DE DOMICILIO//

    let btnCambioDomicilio = $('#btnCambioDomicilio');
    btnCambioDomicilio.attr("disabled", true);

    $('#checkRouterCambioDomicilio').change(function () {

        if (!this.checked) {
            btnCambioDomicilio.attr("disabled", true);
        } else {
            btnCambioDomicilio.attr("disabled", false);
        }
    });

    //************************************************//

    let selectComerciales = $('#selectComerciales');
    let contenedorToggleIncidencia = $('#contenedorToggleIncidencia');
    let incidencia = $('#incidencia');
    let comentarioIncidencia = $('#comentarioIncidencia');
    let tipoIncidencia =$('#tipo');

    selectComerciales.change(function () {
        let opcion = $(this).val();

        if (opcion !== "") {
            contenedorToggleIncidencia.css("display","flex");
        } else {
            contenedorToggleIncidencia.css("display","none");
            $('#toggle-incidencia').prop("checked",false);
            incidencia.css("display", "none");
            comentarioIncidencia.css("display", "none");
            tipoIncidencia.val("");
        }
    });

    incidencia.css("display", "none");
    comentarioIncidencia.css("display", "none");
    contenedorToggleIncidencia.css("display", "none");



    $('#toggle-incidencia').change(function () {

        if (!$(this).is(':checked')) {
            incidencia.css("display", "none");
            comentarioIncidencia.css("display", "none");
        } else {
            incidencia.css("display", "flex");
            comentarioIncidencia.css("display", "block");
        }
    });

    //VALIDACION AL BUSCAR CLIENTES PARA AÑADIRLOS

    let dniBuscarCliente = $('#btnBuscar');
    let dniBuscarInput = $('#dniBuscarInput');
    let mensajedniBuscarInput = $('#mensajedniBuscarInput');



    dniBuscarCliente.click(function(e){
        mensajedniBuscarInput.html("");
        if(dniBuscarInput.val().length !== 9){
            e.preventDefault();
            mensajedniBuscarInput.html('La longitud debe ser de 9 caracteres');
        }
    });

    //VALIDACION AL AÑADIR y MODIFICAR CLIENTES

    let dniAddInput = $('#dni-add-input');
    let btnAddCliente = $('#btnAddCliente');
    let mensajedniAddInput = $('#mensajedniAddInput');
    let mensajeNombreInput = $('#mensajeNombreInput');
    let mensajeDireccionInput =$('#mensajeDireccionInput');
    let mensajeTelefonoInput =$('#mensajeTelefonoInput');
    let inputDireccionAdd = $('#input-direccion-add');
    let inputNombreAdd = $('#input-nombre-add');
    let inputTelefonoAdd = $('#input-telefono-add');

    btnAddCliente.click(function(e){
        mensajedniAddInput.html("");
        mensajeNombreInput.html("");
        mensajeDireccionInput.html("");
        mensajeTelefonoInput.html("");
        if(dniAddInput.val().length !== 9){
            e.preventDefault();
            mensajedniAddInput.html('mínimo 9 caracteres');
        }
        if(inputNombreAdd.val().trim().length === 0){
            e.preventDefault();
            mensajeNombreInput.html('Obligatorio');
        }
        if(inputDireccionAdd.val().trim().length === 0){
            e.preventDefault();
            mensajeDireccionInput.html('Obligatorio');
        }
        if(inputTelefonoAdd.val().trim().length === 0){
            e.preventDefault();
            mensajeTelefonoInput.html('Obligatorio');
        }
        if(inputTelefonoAdd.val().trim().length > 0 && inputTelefonoAdd.val().trim().length < 9 )  {
            e.preventDefault();
            mensajeTelefonoInput.html('mínimo 9 caracteres');
        }
    });

    //**********FIN VALIDACIONES**********//

});





