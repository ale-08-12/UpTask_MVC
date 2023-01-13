<?php

namespace Controllers;

use Models\Proyecto;
use Models\Usuario;
use MVC\Router;

class DashboardController{

    public static function index(Router $router){
        session_start();
        isAuth();

        $proyectos = Proyecto::belongsTo("propietarioId", $_SESSION["id"]);

        $router->render("dashboard/index", [
            "titulo" => "Proyectos",
            "proyectos" => $proyectos
        ]);
    }

    public static function crear_proyecto(Router $router){
        session_start();
        isAuth();

        $alertas = [];

        if($_SERVER["REQUEST_METHOD"] === "POST"){
            $proyecto = new Proyecto($_POST);

            $alertas = $proyecto->validarProyecto();

            if(empty($alertas)){
                // Generar URL Única
                $proyecto->url = md5(uniqid());

                // Almacenar el Creador del Proyecto
                $proyecto->propietarioId = $_SESSION["id"];

                // Guardar el Proyecto y redirecciona
                $proyecto->guardar();
                header("Location: /proyecto?url=" . $proyecto->url);
            }
        }

        $router->render("dashboard/crear-proyecto", [
            "titulo" => "Crear Proyecto",
            "alertas" => $alertas
        ]);
    }

    public static function proyecto(Router $router){
        session_start();
        isAuth();

        $url = $_GET["url"];

        if(!$url) header("Location: /dashboard");

        // Revisar que la persona que visita el proyecto lo haya creado
        $proyecto = Proyecto::buscar("url", $url);

        if($proyecto->propietarioId != $_SESSION["id"])
            header("Location: /dashboard");

        

        $router->render("dashboard/proyecto", [
            "titulo" => $proyecto->proyecto
        ]);
    }

    public static function perfil(Router $router){
        session_start();
        isAuth();

        $usuario = Usuario::buscarID($_SESSION["id"]);

        if($_SERVER["REQUEST_METHOD"] === "POST"){
            $usuario->sincronizar($_POST);
            
            $alertas = $usuario->validar_perfil();

            if(empty($alertas)){
                
                // Verificar que el email no exista en la BD
                $existeUsuario = Usuario::buscar("email", $usuario->email);

                if($existeUsuario && $existeUsuario->id !== $usuario->id){
                    // Alerta de Existe Usuario
                    Usuario::setAlerta("error", "Email no válido, ya pertenece a otra cuenta");
                } else {
                    // Guardar el Usuario
                    $usuario->guardar();

                    // Alerta de Exito
                    Usuario::setAlerta("exito", "Cambios Guardados Correctamente");

                    // Actualizando la sesion con los cambios hechos
                    $_SESSION["nombre"] = $usuario->nombre;
                    $_SESSION["email"] = $usuario->email;
                }
            }
        }
        $alertas = Usuario::getAlertas();

        $router->render("dashboard/perfil", [
            "titulo" => "Perfil",
            "usuario" => $usuario,
            "alertas" => $alertas
        ]);
    }

    public static function cambiar_password(Router $router){
        session_start();
        isAuth();
        
        if($_SERVER["REQUEST_METHOD"] === "POST"){
            $usuario = Usuario::buscarID($_SESSION["id"]);

            // Sincronizamos lo que envio el Usuario
            $usuario->sincronizar($_POST);

            // Comprobamos que los password esten bien
            $alertas = $usuario->nuevo_password();

            if(empty($alertas)){
                
                if($usuario->comprobar_password()){
                    // Sobreescribimos el Password por el Nuevo
                    $usuario->password = $usuario->password_nuevo; 
                    $usuario->hashPassword();
                    $resultado = $usuario->guardar();
                    
                    if($resultado)
                        $alertas = Usuario::setAlerta("exito", "El Password se Cambio Correctamente");
                } else {
                    $alertas = Usuario::setAlerta("error", "El Password Actual es Incorrecto");
                }             
            }
        }
        $alertas = Usuario::getAlertas();
        
        $router->render("dashboard/cambiar-password", [
            "titulo" => "Cambiar Password",
            "alertas" => $alertas
        ]);
    }
}