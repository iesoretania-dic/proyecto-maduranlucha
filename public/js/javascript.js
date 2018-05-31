$(function(){
    "use strict";

    //**********ZONA JS**********//

    // let miSelect = document.getElementById('solucion');
    // let comentario = document.getElementById('comentarioFinalizar');
    // comentario.style.display = "none";

    // miSelect.addEventListener('change',function() {
    //     let opcion = this.options[miSelect.selectedIndex];
    //
    //     if(opcion.innerHTML === "otros"){
    //         comentario.style.display = "block";
    //     }else{
    //         comentario.style.display = "none";
    //     }
    // });

    //**********FIN ZONA JS**********//

    //**********ZONA JQUERY**********//


    $('#miselect').select2();

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
    let mensajeUsuario = $('#cambiosModificarUsuario');
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
    if (mensajeUsuario.html() === 'No'){
        toastr.warning('Cambios no guardados');
    }
    if (mensajeUsuario.html() === 'Si'){
        toastr.success('Cambios guardados');
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

    //***********FINALIZAR INSTALACION*************//

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

    // Activar el comentario de finalizar instalacion

    let solucionFinalizar = $('#solucionFinalizar');
    let comentarioFinalizarInstalacion = $('#comentarioFinalizarInstalacion');
    comentarioFinalizarInstalacion.css("display","none");

    solucionFinalizar.change(function () {
        let opcion = this.options[this.selectedIndex].innerHTML;

        if (opcion === "otros" ) {
            comentarioFinalizarInstalacion.css("display","block");
        } else {
            comentarioFinalizarInstalacion.css("display","none");
        }
    });

    //***********FINALIZAR BAJA*************//

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

    //

    let solucionFinalizarBaja = $('#solucionFinalizarBaja');
    solucionFinalizarBaja.change(function () {

        let opcion = this.options[this.selectedIndex].innerHTML;

        if (opcion === 'Baja parcial') {
            btnBaja.attr("disabled", false);
        } else {
            btnBaja.attr("disabled", true);
        }

        if (opcion === 'otros' || opcion === 'Baja completa') {
            let checkBaja = document.getElementById('checkRouterBaja');
            if(checkBaja.checked){
                btnBaja.attr("disabled", false)
            }else {
                btnBaja.attr("disabled", true);
            }
        }

    });

    //Comentario al finalizar baja

    let comentario = $('#comentarioFinalizar');
    let comentarioBaja = $('#comentarioBaja');
    let mensajeBaja = $('#mensajeBaja');

    comentario.css("display","none");

    solucionFinalizarBaja.change(function () {
        let opcion = this.options[this.selectedIndex].innerHTML;

        if (opcion !== "Baja completa" ) {
            comentario.css("display","block");
        } else {
            comentario.css("display","none");
        }
    });

    btnBaja.click(function(e){
        let selectBaja = document.getElementById('solucionFinalizarBaja');
        let opcion = selectBaja.options[selectBaja.selectedIndex].value;
        if(opcion === 'Baja parcial'){
            if(comentarioBaja.val().trim().length === 0){
                e.preventDefault();
                mensajeBaja.html('Obligatorio');
            }
        }
    });





    //***********FINALIZAR CAMBIO DOMICILIO*************//

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

    //comentario finalizar cambio domicilio

    let solucionFinalizarCambioDomicilio = $('#solucionFinalizarCambioDomicilio');
    let comentarioFinalizarCambioDomicilio = $('#comentarioFinalizarCambioDomicilio');
    comentarioFinalizarCambioDomicilio.css("display","none");

    solucionFinalizarCambioDomicilio.change(function () {
        let opcion = this.options[this.selectedIndex].innerHTML;

        if (opcion === "otros" ) {
            comentarioFinalizarCambioDomicilio.css("display","block");
        } else {
            comentarioFinalizarCambioDomicilio.css("display","none");
        }
    });

    //comentario confirmar finalizar

    let solucionConfimarFinalizar = $('#solucionConfimarFinalizar');
    let comentarioConfirmarFinalizar = $('#comentarioConfirmarFinalizar');
    comentarioConfirmarFinalizar.css("display","none");

    solucionConfimarFinalizar.change(function () {
        let opcion = this.options[this.selectedIndex].innerHTML;

        if (opcion === "otros" ) {
            comentarioConfirmarFinalizar.css("display","block");
        } else {
            comentarioConfirmarFinalizar.css("display","none");
        }
    });



    //************************************************//

    //Control sobre añadir incidencias cuando creamos clientes para el administrador.
    let selectComerciales = $('#selectComerciales');
    let contenedorToggleIncidencia = $('#contenedorToggleIncidencia');
    let comentarioIncidenciaText = $('#otros');
    let comentarioIncidenciaContenedor = $('#comentarioIncidencia');
    let incidencia = $('#incidencia');
    let tipoIncidencia =$('#tipo');

    selectComerciales.change(function () {
        let opcion = $(this).val();

        if (opcion !== "") {
            contenedorToggleIncidencia.css("display","flex");
        } else {
            contenedorToggleIncidencia.css("display","none");
            $('#toggle-incidencia').prop("checked",false);
            incidencia.css("display", "none");
            comentarioIncidenciaText.val("");
            comentarioIncidenciaContenedor.css("display", "none");
            tipoIncidencia.val("");
        }
    });

    incidencia.css("display", "none");
    comentarioIncidenciaContenedor.css("display", "none");
    contenedorToggleIncidencia.css("display", "none");

    $('#toggle-incidencia').change(function () {

        if (!$(this).is(':checked')) {
            incidencia.css("display", "none");
            comentarioIncidenciaContenedor.css("display", "none");
        } else {
            incidencia.css("display", "flex");
            comentarioIncidenciaContenedor.css("display", "block");
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
            mensajedniAddInput.html('9 caracteres');
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

    //VALIDACION AL AÑADIR y MODIFICAR USUARIOS

    let btnUsuarioAdd = $('#btnUsuarioAdd');
    let btnUsuarioModificar = $('#btnUsuarioModificar');

    let usuarioDniAdd = $('#usuarioDniAdd');
    let usuarioUsuarioAdd = $('#usuarioUsuarioAdd');
    let usuarioNombreAdd = $('#usuarioNombreAdd');
    let usuarioTelefonoAdd = $('#usuarioTelefonoAdd');
    let usuarioClaveAdd = $('#usuarioClaveAdd');
    let usuarioClaveRAdd = $('#usuarioClaveRAdd');
    let usuarioAntenas = $('#usuarioAntenas');
    let usuarioRouters = $('#usuarioRouters');
    let usuarioAtas = $('#usuarioAtas');

    let mensajeDniAdd = $('#mensajeDniAdd');
    let mensajeUsuarioAdd = $('#mensajeUsuarioAdd');
    let mensajeNombreAdd = $('#mensajeNombreAdd');
    let mensajeTelefonoAdd = $('#mensajeTelefonoAdd');
    let mensajeClaveAdd = $('#mensajeClaveAdd');
    let mensajeClaveRAdd = $('#mensajeClaveRAdd');
    let mensajeAntenas = $('#mensajeAntenas');
    let mensajeRouters = $('#mensajeRouters');
    let mensajeAtas = $('#mensajeAtas');

    btnUsuarioAdd.click(function(e){
        mensajeDniAdd.html("");
        mensajeUsuarioAdd.html("");
        mensajeNombreAdd.html("");
        mensajeTelefonoAdd.html("");
        mensajeClaveAdd.html("");
        mensajeClaveRAdd.html("");

        if(usuarioDniAdd.val().length !== 9){
            e.preventDefault();
            mensajeDniAdd.html('9 caracteres');
        }
        if(usuarioUsuarioAdd.val().trim().length === 0){
            e.preventDefault();
            mensajeUsuarioAdd.html('Obligatorio');
        }
        if(usuarioNombreAdd.val().trim().length === 0){
            e.preventDefault();
            mensajeNombreAdd.html('Obligatorio');
        }
        if(usuarioTelefonoAdd.val().trim().length === 0){
            e.preventDefault();
            mensajeTelefonoAdd.html('Obligatorio');
        }
        if(usuarioTelefonoAdd.val().trim().length > 0 && usuarioTelefonoAdd.val().trim().length < 9 )  {
            e.preventDefault();
            mensajeTelefonoAdd.html('mínimo 9 caracteres');
        }
        if(usuarioClaveAdd.val().trim().length === 0){
            e.preventDefault();
            mensajeClaveAdd.html('Obligatorio');
        }
        if(usuarioClaveRAdd.val().trim().length === 0){
            e.preventDefault();
            mensajeClaveRAdd.html('Obligatorio');
        }

        if(usuarioClaveAdd.val().trim().length < 5){
            e.preventDefault();
            mensajeClaveAdd.html('Mínimo 5 caracteres');
        }

        if(usuarioClaveRAdd.val().trim().length < 5){
            e.preventDefault();
            mensajeClaveRAdd.html('Mínimo 5 caracteres');
        }

        if(usuarioClaveAdd.val().trim() !== usuarioClaveRAdd.val().trim()){
            e.preventDefault();
            mensajeClaveAdd.html('No coinciden');
            mensajeClaveRAdd.html('No coinciden');
        }

    });
    //Modificar usuarios
    btnUsuarioModificar.click(function(e){
        mensajeDniAdd.html("");
        mensajeUsuarioAdd.html("");
        mensajeNombreAdd.html("");
        mensajeTelefonoAdd.html("");
        mensajeClaveAdd.html("");
        mensajeClaveRAdd.html("");
        mensajeAntenas.html("");
        mensajeRouters.html("");
        mensajeAtas.html("");

        if(usuarioDniAdd.val().length !== 9){
            e.preventDefault();
            mensajeDniAdd.html('9 caracteres');
        }
        if(usuarioUsuarioAdd.val().trim().length === 0){
            e.preventDefault();
            mensajeUsuarioAdd.html('Obligatorio');
        }
        if(usuarioNombreAdd.val().trim().length === 0){
            e.preventDefault();
            mensajeNombreAdd.html('Obligatorio');
        }
        if(usuarioTelefonoAdd.val().trim().length === 0){
            e.preventDefault();
            mensajeTelefonoAdd.html('Obligatorio');
        }
        if(usuarioTelefonoAdd.val().trim().length > 0 && usuarioTelefonoAdd.val().trim().length < 9 )  {
            e.preventDefault();
            mensajeTelefonoAdd.html('Mínimo 9 caracteres');
        }

        if(usuarioClaveAdd.val().trim().length !== 0 || usuarioClaveRAdd.val().trim().length !== 0 )  {
            if(usuarioClaveAdd.val().trim().length === 0){
                e.preventDefault();
                mensajeClaveAdd.html('Obligatorio');
            }
            if(usuarioClaveRAdd.val().trim().length === 0){
                e.preventDefault();
                mensajeClaveRAdd.html('Obligatorio');
            }

            if(usuarioClaveAdd.val().trim().length < 5){
                e.preventDefault();
                mensajeClaveAdd.html('Mínimo 5 caracteres');
            }

            if(usuarioClaveRAdd.val().trim().length < 5){
                e.preventDefault();
                mensajeClaveRAdd.html('Mínimo 5 caracteres');
            }

            if(usuarioClaveAdd.val().trim() !== usuarioClaveRAdd.val().trim()){
                e.preventDefault();
                mensajeClaveAdd.html('No coinciden');
                mensajeClaveRAdd.html('No coinciden');
            }
        }

        if(usuarioAntenas.val().trim().length === 0){
            e.preventDefault();
            mensajeAntenas.html('Obligatorio');
        }
        if(usuarioRouters.val().trim().length === 0){
            e.preventDefault();
            mensajeRouters.html('Obligatorio');
        }
        if(usuarioAtas.val().trim().length === 0){
            e.preventDefault();
            mensajeAtas.html('Obligatorio');
        }

    });

    //VALIDACION AL MODIFICAR DATOS DEL USUARIO DESDE EL PANEL DE CONFIGURACION

    let btnUsuarioConf = $('#btnConfUsuario');

    let usuarioConfUsuario = $('#usuarioConfUsuario');
    let usuarioConfNombre = $('#usuarioConfNombre');
    let usuarioConfClave = $('#usuarioConfClave');
    let usuarioConfClaveR = $('#usuarioConfClaveR');

    let mensajeConfUsuario = $('#mensajeConfUsuario');
    let mensajeConfNombre = $('#mensajeConfNombre');
    let mensajeConfClave = $('#mensajeConfClave');
    let mensajeConfClaveR = $('#mensajeConfClaveR');


    btnUsuarioConf.click(function(e){
        mensajeConfUsuario.html("");
        mensajeConfNombre.html("");
        mensajeConfClave.html("");
        mensajeConfClaveR.html("");

        if(usuarioConfUsuario.val().trim().length === 0){
            e.preventDefault();
            mensajeConfUsuario.html('Obligatorio');
        }
        if(usuarioConfNombre.val().trim().length === 0){
            e.preventDefault();
            mensajeConfNombre.html('Obligatorio');
        }
        if(usuarioConfClave.val().trim().length !== 0 || usuarioConfClaveR.val().trim().length !== 0 )  {

            if(usuarioConfClave.val().trim().length === 0){
                e.preventDefault();
                mensajeConfClave.html('Obligatorio');
            }
            if(usuarioConfClaveR.val().trim().length === 0){
                e.preventDefault();
                mensajeConfClaveR.html('Obligatorio');
            }
            if(usuarioConfClave.val().trim().length < 5){
                e.preventDefault();
                mensajeConfClave.html('Mínimo 5 caracteres');
            }
            if(usuarioConfClaveR.val().trim().length < 5){
                e.preventDefault();
                mensajeConfClaveR.html('Mínimo 5 caracteres');
            }
            if(usuarioConfClave.val().trim() !== usuarioConfClaveR.val().trim()){
                e.preventDefault();
                mensajeConfClave.html('No coinciden');
                mensajeConfClaveR.html('No coinciden');
            }
        }
    });

    //**********FIN VALIDACIONES**********//
});