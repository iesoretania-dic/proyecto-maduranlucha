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
        pageLength: 25
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
        console.log(opcion);

        if (opcion === "otros") {
            comentario.css("display","block");
        } else {
            comentario.css("display","none");
        }
    });


    let btnBaja = $('#btnBaja');
    btnBaja.attr("disabled", true);

    $('#checkRouterBaja').change(function () {

        if (!this.checked) {
            btnBaja.attr("disabled", true);
        } else {
            btnBaja.attr("disabled", false);
        }
    });

    //**********FIN VALIDACIONES**********//

    //FUNCION PARA CAMBIAR EL FORMATO DE LA FECHA Y HORA//

    $('.fechaCambiar').each(function(){
        $(this).html(formato($(this).html()))
    });

    function formato(texto){
        return texto.replace(/^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})$/g,'$3/$2/$1 $4:$5');
    }

});





