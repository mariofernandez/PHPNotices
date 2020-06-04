<?php
switch ($_POST['function']){
    case 'getNoticies':
        GetNotices();
        break;
}
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 03/06/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Apikey para obtener las noticias
 ***********************************************************************/
$api_key = '2eb3747a619644f5becd7a4bd53eb54a';
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 03/06/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Obtenemos las noticias por medio de curl
 ***********************************************************************/
function GetNotices(){
    $total_page_size = 10;
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 03/06/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Creamos una peticion ce cUrl para obtener las noticias
     ***********************************************************************/
    $cURLConnection = curl_init();
    $url = 'https://newsapi.org/v2/top-headlines?apiKey=2eb3747a619644f5becd7a4bd53eb54a';
    if($_POST['search'] !== ''){
        $url.= '&q='.$_POST['search'];
    }else{
        $url.= '&country=mx';
    }
    /***********************************************************************
     *	Autor: Mario Adrián Martínez Fernández   Fecha: 03/06/2020
     *		   mario.martinez.f@hotmail.es
     *	Nota: Se agrega configuracion para la paginacion
     ***********************************************************************/
    $url.= '&pageSize='.$total_page_size;
    if($_POST['page'] !== ''){
        $url.= '&page='.$_POST['page'];
    }
    curl_setopt($cURLConnection, CURLOPT_URL, $url);
    curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);

    $noticesList = curl_exec($cURLConnection);
    curl_close($cURLConnection);

    $notices = json_decode($noticesList);
    if($notices->status === 'error'){
        faildResponse($notices->message);
        return false;
    }
    $total_resultados = intval($notices->totalResults);
    if( $total_resultados> 0){
        /***********************************************************************
         *	Autor: Mario Adrián Martínez Fernández   Fecha: 03/06/2020
         *		   mario.martinez.f@hotmail.es
         *	Nota: Calculamos los resultados contra el tamaño de lo de cada pagina
         *          y obtendremos el total de paginas que podremos obtener
         ***********************************************************************/
        $total_pages = ceil ( ($total_resultados/$total_page_size));
        $notices->totalPages = $total_pages;
        /***********************************************************************
         *	Autor: Mario Adrián Martínez Fernández   Fecha: 03/06/2020
         *		   mario.martinez.f@hotmail.es
         *	Nota: Si la respuesta de las noticias buscadas es correcta hacemos
         *          una iteracion en cada registro para obtener ahora el autor
         *          y remplazamos el que nos regresa la api.
         ***********************************************************************/
        foreach ($notices->articles as $index => $value){
            /***********************************************************************
             *	Autor: Mario Adrián Martínez Fernández   Fecha: 03/06/2020
             *		   mario.martinez.f@hotmail.es
             *	Nota: Obtenemos el autor, para ello hacemos otra peticion cUrl para
             *          obtener a un autor aleatoriamente.
             ***********************************************************************/
            $cURLConnectionAutor = curl_init();
            $url = 'https://randomuser.me/api/';
            curl_setopt($cURLConnectionAutor, CURLOPT_URL, $url);
            curl_setopt($cURLConnectionAutor, CURLOPT_RETURNTRANSFER, true);

            $autor = curl_exec($cURLConnectionAutor);
            curl_close($cURLConnectionAutor);

            $autor_decode = json_decode($autor);
            /***********************************************************************
             *	Autor: Mario Adrián Martínez Fernández   Fecha: 03/06/2020
             *		   mario.martinez.f@hotmail.es
             *	Nota: Una vez que obtenemos el autor, como tiene parametros de first
             *          y last separados, hacemos un concatenar para obtener el nombre
             *          completo del autor.
             ***********************************************************************/
            $name = $autor_decode->results[0]->name;
            $name = $name->first.' '.$name->last;
            /***********************************************************************
             *	Autor: Mario Adrián Martínez Fernández   Fecha: 03/06/2020
             *		   mario.martinez.f@hotmail.es
             *	Nota: Con el nombre concatenado accedemos al arreglo de articles y
             *          mediante el indice(index) en el que se encuenta el arregllo
             *          que estamos recorriendo le setemamos el nombre del autor
             *          nuevo.
             ***********************************************************************/
            $notices->articles[$index]->author = $name;
            $notices->articles[$index]->publishedAt = fechaCastellano ($value->publishedAt,true);

        }
        successResponse($notices,'Listado de noticias');
    }else{
        faildResponse('No existen noticias');
    }


}
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 03/06/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para obtener la fecha en castellano
 ***********************************************************************/
function fechaCastellano ($fecha,$tiempo = false) {
    //$fecha = substr($fecha, 0, 10);
    $numeroDia = date('d', strtotime($fecha));
    $dia = date('l', strtotime($fecha));
    $mes = date('F', strtotime($fecha));
    $anio = date('Y', strtotime($fecha));
    $horas = date('H', strtotime($fecha));
    $minutos = date('i', strtotime($fecha));
    $dias_ES = array("Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo");
    $dias_EN = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
    $nombredia = str_replace($dias_EN, $dias_ES, $dia);
    $meses_ES = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
    $meses_EN = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
    $nombreMes = str_replace($meses_EN, $meses_ES, $mes);
    $texto = $nombredia." ".$numeroDia." de ".$nombreMes." de ".$anio;
    if($tiempo){
        $texto.=" - ".$horas.":".$minutos;
    }
    return $texto;
}
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 03/06/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para retornar respuesta correcta
 ***********************************************************************/
function successResponse( $data , $msg, $extras='',$merge='')
{
    http_response_code(200);
    $response = array();
    $response['status_code'] = 200;
    $response['success'] = true;
    $response['msg'] = $msg;
    $response['extras'] = $extras;
    $response['data'] = $data;
    if($merge !== ''){
        $response = array_merge($response, $merge);
    }
    output_json($response);
}
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 03/06/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Funcion para retonar respuesta en caso de que sea erronea
 ***********************************************************************/
function faildResponse( $msg,$extras='',$merge='')
{
    http_response_code(401);
    $response = array();
    $response['success'] = false;
    $response['error_code'] = 401;
    $response['error_msg'] = $msg;
    $response['extras'] = $extras;
    if($merge !== ''){
        $response = array_merge($response, $merge);
    }
    output_json($response);

}
/***********************************************************************
 *	Autor: Mario Adrián Martínez Fernández   Fecha: 03/06/2020
 *		   mario.martinez.f@hotmail.es
 *	Nota: Codifica la respuesta creada
 ***********************************************************************/
function output_json($response)
{
    echo json_encode($response, JSON_NUMERIC_CHECK);
}

?>
