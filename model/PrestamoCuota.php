<?php

class PrestamoCuota {
    private $id_prestamo_cutoa;
    private $id_prestamo;
    private $monto;
    private $fecha_calc;
    private $fecha_pago;
    private $estado;
    private $descripcion;
    
function __construct() {
     $this->id_prestamo_cutoa=null;
     $this->id_prestamo=null;
     $this->monto=null;
     $this->fecha_calc=null;
     $this->fecha_pago=null;
     $this->estado=null;
     $this->descripcion=null;
}

public function getId_prestamo_cutoa() {
    return $this->id_prestamo_cutoa;
}

public function setId_prestamo_cutoa($id_prestamo_cutoa) {
    $this->id_prestamo_cutoa = $id_prestamo_cutoa;
}

public function getId_prestamo() {
    return $this->id_prestamo;
}

public function setId_prestamo($id_prestamo) {
    $this->id_prestamo = $id_prestamo;
}

public function getMonto() {
    return $this->monto;
}

public function setMonto($monto) {
    $this->monto = $monto;
}

public function getFecha_calc() {
    return $this->fecha_calc;
}

public function setFecha_calc($fecha_calc) {
    $this->fecha_calc = $fecha_calc;
}

public function getFecha_pago() {
    return $this->fecha_pago;
}

public function setFecha_pago($fecha_pago) {
    $this->fecha_pago = $fecha_pago;
}

public function getEstado() {
    return $this->estado;
}

public function setEstado($estado) {
    $this->estado = $estado;
}

public function getDescripcion() {
    return $this->descripcion;
}

public function setDescripcion($descripcion) {
    $this->descripcion = $descripcion;
}


}

?>