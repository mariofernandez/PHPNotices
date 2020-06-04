var url = 'http://localhost:81/PHPNotices/';
$(document).ready(function(){
    LoadNotices(1);
});
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 03/06/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para obtener las noticias
 ***********************************************************************/
var pagecurrent = 0,
    totalpage = 0;
function LoadNotices(page) {
    var datos = new FormData();
    datos.append('function','getNoticies');
    datos.append('search',$('#search').val());
    datos.append('page',page);
    pagecurrent = page;
    var config = {
        url: url+"notices.php",
        type: "POST",
        cache: false,
        contentType:false,
        processData: false,
        data: datos,
        success: function(response) {
            var data = JSON.parse(response).data;
            totalpage = parseInt(data.totalPages);
            CratePaginator(data.totalPages,page);
            CreateTableNotices(data);
        },
        error: function (response) {
            alert(JSON.parse(response.responseText).error_msg);
        }
    }
    $.ajax(config);
}
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 03/06/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Crear tabla de noticias con los datos obtenido del sevicio
 ***********************************************************************/
function CreateTableNotices(data) {
    let html = '';
    $.each(data.articles,function (index,value) {
        html += '<div class="row justify-content-center pb-3 pt-2 text-center" style="border-bottom: 2px solid darkgrey;">' +
            '<div class="col-11 lead">' +
                '<div class="row justify-content-center">' +
                    '<div class="col-6 text-center">' +
                        '<h4>'+value.title+'</h4>' +
                        '<div class="h5 pt-1">'+value.publishedAt+'</div>' +
                    '</div>' +
                '</div>' +
                '<img class="w-50" src="'+value.urlToImage+'">'+
                '<div class="row justify-content-center">' +
                    '<div class="text-justify col-10">'+value.description+' <a target="_blank" href="'+value.url+'">ver más...</a></div>' +
                '</div>' +
            '</div>' +
        '</div>';
    });
    $('#list_notices').html(html)
}
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 03/06/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para creacion de paginador en base a los resuntados
 *          recibidos por el servicio.
 ***********************************************************************/
function CratePaginator(contador,page) {
    if(contador > 0){
        let html = '<li class="page-item"><a class="page-link" onclick="SiguienteRetroceder(\'atras\')" href="javascript:void(0);">Atras</a></li>';
        for (var i = 1; i <= contador; i++) {
            let active = (i === page)?'active':'';
            html+='<li class="page-item '+active+'"><a class="page-link" onclick="LoadNotices('+i+')" href="javascript:void(0);">'+i+'</a></li>';
        }
        html+='<li class="page-item"><a class="page-link" onclick="SiguienteRetroceder(\'siguiente\')"  href="javascript:void(0);">Siguiente</a></li>';
        $('#paginator_notices').html(html);
    }
}
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 03/06/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Pasar a la siguiente pagina o retroceder
 ***********************************************************************/
function SiguienteRetroceder(tipo) {
    if(tipo === 'siguiente'){
        pagecurrent = pagecurrent + 1;
        pagecurrent = (pagecurrent > totalpage)?pagecurrent - 1: pagecurrent;
    }else{
        pagecurrent = pagecurrent - 1;
        pagecurrent = (pagecurrent <= 0)?pagecurrent + 1: pagecurrent;
    }
    LoadNotices(pagecurrent);
}
