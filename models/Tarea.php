<?php

namespace Models;

class Tarea extends ActiveRecord{
    protected static $tabla = "tareas";
    protected static $columnasDB = ["id", "nombre", "estado", "proyectoId"];

    public function __construct($args = []){
        $this->id = $args["id"] ?? NULL;
        $this->nombre = $args["nombre"] ?? "";
        $this->estado = $args["estado"] ?? 0;
        $this->proyectoId = $args["proyectoId"] ?? "";
    }
}