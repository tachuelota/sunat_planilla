<?php

$op = $_REQUEST["oper"];
if ($op) {
    session_start();
    require_once '../util/funciones.php';
    require_once '../dao/AbstractDao.php';
    // IDE_EMPLEADOR_MAESTRO
    require_once '../controller/ideController.php';

    // IDE CONFIGURACION 
    require_once '../controller/ConfController.php';


    //Actualizar PLAME   
    require_once '../dao/PlameDeclaracionDao.php';
    require_once '../dao/TrabajadorPdeclaracionDao.php';
    require_once '../dao/DeclaracionDconceptoDao.php';

    require_once '../model/DeclaracionDconcepto.php';

    // AFP
    require_once '../model/ConfAfp.php';
    require_once '../dao/ConfAfpDao.php';
    require_once '../controller/ConfAfpController.php';
}
????
$responce = NULL;
if ($op == "add") {
    //$responce = add_PtrabajadorPdeclaracion();
} else if ($op == "generar_declaracion") {
    generarDeclaracionPlanillaMensual();
}


echo (!empty($responce)) ? json_encode($responce) : '';

function generarDeclaracionPlanillaMensual() {
//==============================================================================
    $ID_PDECLARACION = $_REQUEST['id_pdeclaracion'];

    $dao = new PlameDeclaracionDao();
    $data = $dao->listarDeclaracionEtapa($ID_PDECLARACION);
    echo "<pre>";
    print_r($data);
    echo "</pre>";
    // paso 01 :: Get todos los -> id_trabajador
    $ID_TRABAJADOR = array();
    for ($i = 0; $i < count($data); $i++) {
        $ID_TRABAJADOR[] = $data[$i]['id_trabajador'];
    }
    $data = null;
//==============================================================================
    //paso 02 :: Registrar [trabajadores_pdeclaraciones]


    for ($i = 0; $i < count($ID_TRABAJADOR); $i++) {

        //-----------------------------------------------------------
        //REGISTRAMOS TRABAJADOR (declaracion Mensual)
        $dao = new TrabajadorPdeclaracionDao();
        $id_trabajador_pdeclaracion = $dao->registrar($ID_PDECLARACION, $ID_TRABAJADOR[$i]);
        echo "id_trabajador_pdeclaracion = " . $id_trabajador_pdeclaracion;

        // paso 03 :: Consultar Conceptos
        // INGRESOS
        ECHO "SEULDO BASICO";
        concepto_0121($id_trabajador_pdeclaracion);
        //Asignacion familiar
        ECHO "ASIGNACION FAMILIAR";
        concepto_0201($id_trabajador_pdeclaracion);

        // DESCUENTOS
        ECHO "DESCUENTO";
        concepto_0701($ID_TRABAJADOR[$i], $ID_PDECLARACION, $id_trabajador_pdeclaracion);
        //????
        // paso 04 :: Preguntar si el trabajador cumple:
        // TRIBUTOS Y APORTACIONES

        $DATA_TRA = $dao->buscar_ID_trabajador($ID_TRABAJADOR[$i]);
        // Regimen de Salud
        if ($DATA_TRA['cod_regimen_aseguramiento_salud'] == '00') { //ok Regimen de Salud Regular
            concepto_0804($id_trabajador_pdeclaracion);
        } else {
            // null 
        }

        // Regimen Pensionario
        //AFP 


        if ($DATA_TRA['cod_regimen_pensionario'] == '02') { //ONP
            concepto_0607($id_trabajador_pdeclaracion);
        } else if ($DATA_TRA['cod_regimen_pensionario'] == '21') { //Integra            
            concepto_AFP($id_trabajador_pdeclaracion, '21');
        } else if ($DATA_TRA['cod_regimen_pensionario'] == '22') { //horizonte
            concepto_AFP($id_trabajador_pdeclaracion, '22');
        } else if ($DATA_TRA['cod_regimen_pensionario'] == '23') { //Profuturo
            concepto_AFP($id_trabajador_pdeclaracion, '23');
        } else if ($DATA_TRA['cod_regimen_pensionario'] == '24') { //Prima
            concepto_AFP($id_trabajador_pdeclaracion, '24');
        } else {
            //null
        }


        //Otra utilidades
        if ($DATA_TRA['aporta_essalud_vida'] == '1') { // ESSALUD_MAS
            concepto_0604($id_trabajador_pdeclaracion);
        }

        if ($DATA_TRA['aporta_asegura_tu_pension'] == '1') { //ASEGURA PENSION_MAS
            concepto_0612($id_trabajador_pdeclaracion);
        }
        //-----------------------------------------------------------
    }//ENDFOR
}

// Sueldo Basico
function concepto_0121($id_trabajador_pdeclaracion) {

    //SUELDO BASICO
    $SUELDO_BASE = SB;
    $model = new DeclaracionDconcepto();
    $model->setId_trabajador_pdeclaracion($id_trabajador_pdeclaracion);
    $model->setMonto_devengado($SUELDO_BASE);
    $model->setMonto_pagado($SUELDO_BASE);
    $model->setCod_detalle_concepto('0121');

    $dao = new DeclaracionDconceptoDao();

    return $dao->registrar($model);
}

// ASIGNACION FAMILIAR
function concepto_0201($id_trabajador_pdeclaracion) {
    //SUELDO BASICO
    $CAL_AF = SB * (T_AF / 100);
    $model = new DeclaracionDconcepto();
    $model->setId_trabajador_pdeclaracion($id_trabajador_pdeclaracion);
    $model->setMonto_devengado($CAL_AF);
    $model->setMonto_pagado($CAL_AF);
    $model->setCod_detalle_concepto('0201');

    $dao = new DeclaracionDconceptoDao();    
    $dao->registrar($model);
    
    return true;
}

// Adelanto en este caso la suma de las 2 QUINCENAS ?????????? DUDAAA!!! 
function concepto_0701($ID_TRABAJADOR, $ID_PDECLARACION, $id_trabajador_pdeclaracion) {

    // 01 :: = Consultar Trabajador
    $dao = new PlameDeclaracionDao();
    $ADELANTO = $dao->PrimerAdelantoMensual($ID_TRABAJADOR, $ID_PDECLARACION);


    // 02 ::    
    //ADELANTO    
    $model = new DeclaracionDconcepto();
    $model->setId_trabajador_pdeclaracion($id_trabajador_pdeclaracion);
    $model->setMonto_devengado($ADELANTO);
    $model->setMonto_pagado($ADELANTO);
    $model->setCod_detalle_concepto('0701');

    $dao = new DeclaracionDconceptoDao();
    return $dao->registrar($model);
}

// RENTA DE QUINTA CATEGORIA
function concepto_0605($id_trabajador_pdeclaracion) {
    /*
      $CAL_AF = SB * (T_AF/100);
      $model = new DeclaracionDconcepto();
      $model->setId_trabajador_pdeclaracion($id_trabajador_pdeclaracion);
      $model->setMonto_devengado($CAL_AF);
      $model->setMonto_pagado($CAL_AF);
      $model->setCod_detalle_concepto('0201');

      $dao = new DeclaracionDconceptoDao();
      echo "<pre>";
      print_r($model);
      echo "</pre>";
      $dao->registrar($model);
     */
}

// SNP [ONP = 02]
function concepto_0607($id_trabajador_pdeclaracion) {

    //====================================================          
    $all_ingreso = allConceptos_Afectos_Ingreso($id_trabajador_pdeclaracion);
    //ECHO " all_ingreso = " . $all_ingreso;
    //====================================================

    $CALC = (floatval($all_ingreso)) * (T_ONP / 100);

    $model = new DeclaracionDconcepto();
    $model->setId_trabajador_pdeclaracion($id_trabajador_pdeclaracion);
    $model->setMonto_devengado($CALC);
    $model->setMonto_pagado($CALC);
    $model->setCod_detalle_concepto('0607');
    //dao
    $dao = new DeclaracionDconceptoDao();
    return $dao->registrar($model);
}

// AFP o SPP 
// 0601 = Comision afp porcentual
// 0606 = Prima de suguro AFP
// 0608 = SPP aportacion obligatoria
function concepto_AFP($id_trabajador_pdeclaracion, $cod_regimen_pensionario) {

    //====================================================          
    $all_ingreso = allConceptos_Afectos_Ingreso($id_trabajador_pdeclaracion);
    //ECHO " all_ingreso = " . $all_ingreso;
    //====================================================    
    //----
    $afp = new ConfAfp();
    $afp = vigenteAfp($cod_regimen_pensionario);
    //----

    $A_OBLIGATORIO = floatval($afp->getAporte_obligatorio());
    $COMISION = floatval($afp->getComision());
    $PRIMA_SEGURO = floatval($afp->getPrima_seguro());


    // UNO = comision porcentual
    $_601 = (floatval($all_ingreso)) * ($COMISION / 100);

    // DOS prima de seguro
    $_606 = (floatval($all_ingreso)) * ($PRIMA_SEGURO / 100);
    
    // TRES = aporte obligatorio
    $_608 = (floatval($all_ingreso)) * ($A_OBLIGATORIO / 100);


    // uno DAO
    $model = new DeclaracionDconcepto();
    $model->setId_trabajador_pdeclaracion($id_trabajador_pdeclaracion);
    //$model->setMonto_devengado($CALC);
    $model->setMonto_pagado($_601);
    $model->setCod_detalle_concepto('0601');
    $dao = new DeclaracionDconceptoDao();
    $dao->registrar($model);
    
    
    // dos DAO
    $model = new DeclaracionDconcepto();
    $model->setId_trabajador_pdeclaracion($id_trabajador_pdeclaracion);
    //$model->setMonto_devengado($CALC);
    $model->setMonto_pagado($_606);
    $model->setCod_detalle_concepto('0606');
    $dao = new DeclaracionDconceptoDao();
    $dao->registrar($model);  
    
    // tres DAO
    $model = new DeclaracionDconcepto();
    $model->setId_trabajador_pdeclaracion($id_trabajador_pdeclaracion);
    //$model->setMonto_devengado($CALC);
    $model->setMonto_pagado($_608);
    $model->setCod_detalle_concepto('0608');
    $dao = new DeclaracionDconceptoDao();
    $dao->registrar($model);    
    
    return true;
}

// 604 ESSALUD + VIDA
function concepto_0604($id_trabajador_pdeclaracion) {

    $CALC = ESSALUD_MAS;
    $model = new DeclaracionDconcepto();
    $model->setId_trabajador_pdeclaracion($id_trabajador_pdeclaracion);
    //$model->setMonto_devengado($CALC);
    $model->setMonto_pagado($CALC);
    $model->setCod_detalle_concepto('0604');

    $dao = new DeclaracionDconceptoDao();
    return $dao->registrar($model);
}

// 612 SNP ASEGURA TU PENSIÓN +
function concepto_0612($id_trabajador_pdeclaracion) {
    $CALC = SNP_MAS;
    $model = new DeclaracionDconcepto();
    $model->setId_trabajador_pdeclaracion($id_trabajador_pdeclaracion);
    // $model->setMonto_devengado($CALC);
    $model->setMonto_pagado($CALC);
    $model->setCod_detalle_concepto('0612');

    $dao = new DeclaracionDconceptoDao();
    return $dao->registrar($model);
}

/*
 * {Funcion ayudante} OjO conceptos bien ordenados
 */

function allConceptos_Afectos_Ingreso($id_trabajador_pdeclaracion) {

    //====================================================
    //DAO
    $dao = new DeclaracionDconceptoDao();
    $DATA = $dao->buscar_ID_TrabajadorPdeclaracion($id_trabajador_pdeclaracion);

    $monto = 0;
    for ($i = 0; $i < count($DATA); $i++) {
        if ($DATA[$i]['cod_detalle_concepto'] == '0121') {
            // Sueldo basico
            $monto = $monto + (floatval($DATA[$i]['monto_pagado']));
            //continue;           
        } else if ($DATA[$i]['cod_detalle_concepto'] == '0201') {
            // Asignacion Familiar
            $monto = $monto + (floatval($DATA[$i]['monto_pagado']));
        } else if ($DATA[$i]['cod_detalle_concepto'] == '0107') {
            //trabajo en feriado o dia descanso  -----------------PENDIENTE
            $monto = $monto + (floatval($DATA[$i]['monto_pagado']));
        } else if ($DATA[$i]['cod_detalle_concepto'] == '0118') {
            //Remuneracion Vacacional *?*   -----------------PENDIENTE
            $monto = $monto + (floatval($DATA[$i]['monto_pagado']));
        }
    }
    //====================================================
    return $monto;
}

// 804 ESSALUD trabajador
function concepto_0804($id_trabajador_pdeclaracion) {
    //====================================================          
    $all_ingreso = allConceptos_Afectos_Ingreso($id_trabajador_pdeclaracion);
    //====================================================

    $CALC = floatval($all_ingreso) * (T_ESSALUD / 100);

    $model = new DeclaracionDconcepto();
    $model->setId_trabajador_pdeclaracion($id_trabajador_pdeclaracion);
    $model->setMonto_devengado($CALC);
    $model->setMonto_pagado($CALC);
    $model->setCod_detalle_concepto('0804');

    $dao = new DeclaracionDconceptoDao();
    return $dao->registrar($model);
}

