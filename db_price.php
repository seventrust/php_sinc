<?php



//conectar a las bases de datos
$connect = mysqli_connect("host","usuario","contrasena!","db");
//Obtener el Valor del Dolar por hora
//Esta parte del código es para efectuar un cálculo antes de hacer el UPDATE en el precio
$xmlSource = "http://indicadoresdeldia.cl/webservice/indicadores.xml";
$xml = simplexml_load_file($xmlSource);
//Se hace un Substring en la variable, para evitar traernos el símbolo $

$dSub = substr($xml->moneda->dolar,1);
$d_val = floatval($dSub);

//Descargar el archivo para sincronizar
//El archivo .CVS puede estar en local en un hosting http o un ftp
$file = 'ARCHIVO.csv';
if (($handle = fopen($file, "r")) !== FALSE) {
   fgetcsv($handle);   
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $num = count($data);
        for ($c=0; $c < $num; $c++) {
          $col[$c] = $data[$c];
        }

            $col1 = $col[0];//id
            $col2 = $col[1];//part_number
            $col3 = $col[2];//codigo
            $col4 = $col[3];//descripcion
            $col5 = $col[4];//marca
            $col6 = $col[5];//categoria
            $col7 = $col[6];//subcategoria
            $col8 = $col[7];//precio
            $col9 = $col[8];//moneda
            $col10 = $col[9];//cantidad
            //Se verifica el símbolo de la moneda para la conversión del valor total
            if($col9 == "US$"){
              //Se efectúa unn redondeo hacia arriba
                $precio_final = ceil($col8 * floatval(1.24));					
            }
            else
                {
                $dolar_final = round($d_val + floatval(5.0));
                $precio_final = ceil($col8 / $dolar_final);
                
                }
            
            // SQL Query Para hacer el update de los datos... 
            $query = "UPDATE ps_product SET price = '".$precio_final."' WHERE reference = '".$col2."'";
            $query2 = "UPDATE ps_stock_available a JOIN ps_product b ON a.id_product = b.id_product SET a.quantity = '".$col10."' WHERE b.reference = '".$col2."'";
            $s = mysqli_query($connect, $query);
            $s2 = mysqli_query($connect, $query2);
 }
    fclose($handle);
}

echo "Archivo sincronizado!!!";
mysqli_close($connect);
