<?php
//*******************************************************************//
require_once('../view/ide.php');
//*******************************************************************//
require_once '../controller/ideController.php';
$data = $_SESSION['sunat_empleador'];
$id_declaracion = $_REQUEST['id_declaracion'];

$PERIODO = ($_REQUEST['periodo']) ? $_REQUEST['periodo'] : "00/0000";

//echo "DDDDDDDDDD".$PERIODO;

?>
<script type="text/javascript">
//VARIABLES GLOBALES
//var PERIODOX = '<?php //echo $PERIODO;?>';

    $(document).ready(function(){
                  
        $( "#tabs2").tabs();
		
	});
	
	
	cargar_pagina('sunat_planilla/view-plame/detalle_declaracion/view_trabajador.php?id_declaracion='+ID_DECLARACION ,'#tabs-2-1');
	//cargar_pagina('sunat_planilla/view-plame/declaraciones_detalle/view_trabajador.php','#tabs-2-2');
	//cargar_pagina('sunat_planilla/view-plame/detalle_declaracion/view_p4ta_catecoria.php?periodo='+$PERIODO ,'#tabs-2-3');

	
	cargarTablaTrabajadorPdeclaracion(ID_DECLARACION);
	//cargarTablaPTrabajadores(ID_DECLARACION);
	
	// funciones
	
	

   //02  = total
	$("#reporte30_02").click(function(){
		
		//registrarEtapa(null);
		var url = "sunat_planilla/controller/TrabajadorPdeclaracionController.php";
		url +="?oper=recibo30&id_pdeclaracion="+ID_DECLARACION
		url +="&todo=todo";
		
		window.location.href = url;
		//window.open(url);

	});
	
	   //02  = total
	$("#reporte30_mas").click(function(){
		console.log("crear dialog");
		//editarPagoMasOpciones(id_pdeclaracion,id_etapa_pago);
		/*
		var url = "sunat_planilla/controller/PagoController.php";
		url +="?oper=recibo15&id_pdeclaracion="+id_pdeclaracion+"&id_etapa_pago="+id_etapa_pago;
		
		window.location.href = url;
		//window.open(url);
		*/

	});
	
	
</script>


<div align="left">

RUC: <?php echo $data['ruc']. " - ". $data['razon_social_concatenado']; ?>
    <div id="tabs2">
    
   
        <ul>
            <li><a href="#tabs-2-1">Trabajadores</a></li>
            <!--<li><a href="#tabs-2-2">Pensionistas</a></li>	
            <li><a href="#tabs-2-3">PS 4Ta Categoria</a></li>-->		

        </ul>
        <div id="tabs-2-1">
        11111        
        </div>
        <!--<div id="tabs-2-2">
        22222        
        </div>
        <div id="tabs-2-3">
        3333        
        </div>-->
        
        
</div>

</div>

