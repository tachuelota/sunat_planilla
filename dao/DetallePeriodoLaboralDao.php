<?php

/**
 * Description of DetalleEstablecimientoVinculadoDao
 *
 * @author conta 1
 */
class DetallePeriodoLaboralDao extends AbstractDao {

    //put your code here

    public function registrarDetallePeriodoLaboral($obj) {
        $query = "
        INSERT INTO detalle_periodos_laborales
        (
            id_trabajador,
            cod_motivo_baja_registro,
            fecha_inicio,
            fecha_fin,
            id_persona)
        VALUES (
            ?,
            ?,
            ?,
            ?,
            ?);
        ";

        $model = new DetallePeriodoLaboral();
        $model = $obj;

        $stm = $this->pdo->prepare($query);
        $stm->bindValue(1, $model->getId_trabajador());
        $stm->bindValue(2, $model->getCod_motivo_baja_registro());
        $stm->bindValue(3, $model->getFecha_inicio());
        $stm->bindValue(4, $model->getFecha_fin());
        $stm->bindValue(5, $model->getId_persona());
        $stm->execute();
        //$data = $stm->fetchAll();
        return true;
    }

    public function actualizarDetallePeriodoLaboral($obj) {
        $query = "            
        UPDATE detalle_periodos_laborales
        SET 
          cod_motivo_baja_registro = ?,
          fecha_inicio = ?,
          fecha_fin = ?
        WHERE id_detalle_periodo_laboral = ?;        
        ";
        $model = new DetallePeriodoLaboral();
        $model = $obj;
        $stm = $this->pdo->prepare($query);
        $stm->bindValue(1, $model->getCod_motivo_baja_registro());
        $stm->bindValue(2, $model->getFecha_inicio());
        $stm->bindValue(3, $model->getFecha_fin());
        $stm->bindValue(4, $model->getId_detalle_periodo_laboral());
        
        
        $stm->execute();
        return true;
    }

    function eliminarDetallePeriodoLaboral($id) {
        $query = "
        DELETE FROM detalle_periodos_laborales WHERE id_detalle_periodo_laboral = ?;";
        $stm = $this->pdo->prepare($query);
        $stm->bindValue(1, $id);
        $stm->execute();
    }

    public function listarDetallePeriodoLaboral($id_trabajador) {

        $query = "???????";

        $stm = $this->pdo->prepare($query);
        $stm->bindValue(1, $id_trabajador);
        $stm->execute();
        $data = $stm->fetchAll();
        return $data;
    }

    //------- buscador

    function buscarDetallePeriodoLaboral($id_TRA){
       // $query = "SELECT *FROM detalle_periodos_laborales WHERE id_trabajador = ?";
        $query ="
        SELECT 
            id_detalle_periodo_laboral,
            id_trabajador,
            fecha_inicio,
            fecha_fin,
            cod_motivo_baja_registro
        FROM detalle_periodos_laborales 
        WHERE (id_trabajador = ?)
        ORDER BY id_detalle_periodo_laboral DESC            
    ";
        $stm = $this->pdo->prepare($query);
        $stm->bindValue(1, $id_TRA);
        //$stm->bindValue(2, $id_persona);
        $stm->execute();
        $data = $stm->fetchAll();
        return $data[0];
    }
    
    
    // funcion reporte  boleta mensual
 function buscarDetallePeriodoLaboral_2($id_trabajador) {
       
        $query ="
        SELECT 
            fecha_inicio            
        FROM detalle_periodos_laborales 
        WHERE (id_trabajador = ?)
        ORDER BY id_detalle_periodo_laboral DESC            
    ";
        $stm = $this->pdo->prepare($query);
        $stm->bindValue(1, $id_trabajador);        
        $stm->execute();
        $data = $stm->fetchAll();
        return $data[0]['fecha_inicio'];
    }
    
    
    
    

}

?>