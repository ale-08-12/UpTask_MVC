<?php

namespace Controllers;

use Models\Proyecto;
use Models\Tarea;

class TareaController{
    public static function index(){
        $proyectoURL = $_GET["url"] ?? null;

        if(!$proyectoURL)
            header("Location: /dashboard");

        session_start();

        $proyecto = Proyecto::buscar("url", $proyectoURL);

        if(!$proyecto || $proyecto->propietarioId != $_SESSION["id"])
            header("Location: /404");

        $tareas = Tarea::belongsTo("proyectoId", $proyecto->id);

        echo json_encode(["tareas" => $tareas]);
    }

    public static function crear(){
        if($_SERVER["REQUEST_METHOD"] === "POST"){
            session_start();
            
            $proyecto = Proyecto::buscar("url", $_POST["proyectoURL"]);

            if(!$proyecto || $proyecto->propietarioId != $_SESSION["id"]){
                $respuesta = [
                    "tipo" => "error",
                    "mensaje" => "Hubo un Error al Agregar la Tarea"
                ];
                echo json_encode($respuesta);
                return;
            }

            // Si esta todo bien, Instanciar y crear la Tarea
            $tarea = new Tarea($_POST);
            $tarea->proyectoId = $proyecto->id;
            $resultado = $tarea->guardar();

            if($resultado){
                $respuesta = [
                    "tipo" => "exito",
                    "mensaje" => "Tarea Creada Correctamente",
                    "id" => $resultado["id"],
                    "proyectoId" => $tarea->proyectoId
                ];
                echo json_encode($respuesta);
            }
        }
    }

    public static function actualizar(){
        if($_SERVER["REQUEST_METHOD"] === "POST"){
            session_start();

            // Validar que el Proyecto Exista
            $proyecto = Proyecto::buscar("url", $_POST["proyectoURL"]);

            if(!$proyecto || $proyecto->propietarioId != $_SESSION["id"]){
                $respuesta = [
                    "tipo" => "error",
                    "mensaje" => "Hubo un Error al Actualizar la Tarea"
                ];
                echo json_encode($respuesta);
                return;
            }

            $tarea = new Tarea($_POST);
            $resultado = $tarea->guardar();

            if($resultado){
                $respuesta = [
                    "tipo" => "exito",
                    "mensaje" => "Actualizado Correctamente",
                    "id" => $tarea->id,
                    "proyectoId" => $tarea->proyectoId
                ];
                echo json_encode($respuesta);
            } 
        }
    }

    public static function eliminar(){
        if($_SERVER["REQUEST_METHOD"] === "POST"){
            session_start();

            // Validar que el Proyecto Exista
            $proyecto = Proyecto::buscar("url", $_POST["proyectoURL"]);

            if(!$proyecto || $proyecto->propietarioId != $_SESSION["id"]){
                $respuesta = [
                    "tipo" => "error",
                    "mensaje" => "Hubo un Error al Eliminar la Tarea"
                ];
                echo json_encode($respuesta);
                return;
            }

            $tarea = new Tarea($_POST);
            $resultado = $tarea->eliminar();

            if($resultado){
                $respuesta = [
                    "tipo" => "exito",
                    "mensaje" => "Eliminado Correctamente"
                ];
                echo json_encode($respuesta);
            }
        }
    }
}