<?php

namespace Controllers;

use MVC\Router;
use Classes\Email;
use Models\Usuario;

class LoginController {
    public static function login(Router $router){

        $alertas = [];

        if($_SERVER["REQUEST_METHOD"] === "POST"){
            $usuario = new Usuario($_POST);

            $alertas = $usuario->validarLogin();

            if(empty($alertas)){
                // Verificar que el Usuario Exista
                $usuario = Usuario::buscar("email", $usuario->email);

                if(!$usuario || !$usuario->confirmado){
                    Usuario::setAlerta("error", "El Usuario No Existe o No esta Confirmado");
                }else{
                    if(!password_verify($_POST["password"], $usuario->password)){
                        Usuario::setAlerta("error", "El Password es Incorrecto");
                    }else{
                        // Iniciar la Session del Usuario
                        session_start();
                        $_SESSION["id"] = $usuario->id;
                        $_SESSION["nombre"] = $usuario->nombre;
                        $_SESSION["email"] = $usuario->email;
                        $_SESSION["login"] = TRUE;

                        // Redireccionar
                        header("Location: /dashboard");
                    }
                }
            }      
        }
        $alertas = Usuario::getAlertas();

        // Render a la vista
        $router->render("auth/login", [
            "titulo" => "Iniciar Sesión",
            "alertas" => $alertas
        ]);
    }

    public static function logout(){
        session_start();
        $_SESSION = [];
        header("Location: /");
    }

    public static function crear(Router $router){

        $usuario = new Usuario;
        $alertas = [];
        
        if($_SERVER["REQUEST_METHOD"] === "POST"){
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();

            if(empty($alertas)){
                $existeUsuario = Usuario::buscar("email", $usuario->email);

                if($existeUsuario){
                    Usuario::setAlerta("error", "El Usuario ya esta registrado");
                    $alertas = Usuario::getAlertas();
                }else{
                    // Hashear Password
                    $usuario->hashPassword();

                    // Eliminar password2
                    unset($usuario->password2);

                    // Generar token
                    $usuario->crearToken();

                    // Crear un nuevo usuario
                    $resultado = $usuario->guardar();

                    // Enviar email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);

                    if($resultado){
                        $email->enviarConfirmacion();
                        header("Location: /mensaje");
                    }
                }
            }
        }

        // Render a la vista
        $router->render("auth/crear", [
            "titulo" => "Crear tu Cuenta",
            "usuario" => $usuario,
            "alertas" => $alertas
        ]);
    }

    public static function olvide(Router $router){

        $alertas = [];

        if($_SERVER["REQUEST_METHOD"] === "POST"){
            $usuario = new Usuario($_POST);
            $alertas = $usuario->validarEmail();

            if(empty($alertas)){
                // Buscar el Usuario
                $usuario = Usuario::buscar("email", $usuario->email);

                if($usuario && $usuario->confirmado){
                    // Generar Nuevo Token
                    $usuario->crearToken();
                    unset($usuario->password2);

                    // Actualizar el Usuario
                    $resultado = $usuario->guardar();

                    // Enviar el Email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);

                    if($resultado){
                        $email->enviarRestablecer();

                        // Imprimir la Alerta
                        Usuario::setAlerta("exito", "Hemos Enviado las Instrucciones a tu Email");
                    }else{
                        Usuario::setAlerta("error", "No se Pudo Reestrablecer Usuario");
                    }
                }else{
                    Usuario::setAlerta("error", "El Usuario no Existe o no esta Confirmado");
                }
            } 
        }

        $alertas = Usuario::getAlertas();

        // Render a la vista
        $router->render("auth/olvide", [
            "titulo" => "Olvide mí Password",
            "alertas" => $alertas
        ]);
    }

    public static function reestablecer(Router $router){

        $token = s($_GET["token"]);
        $mostrar = true;
        
        if(!$token)
            header("Location: /");

        // Identificar el Usuario con el Token
        $usuario = Usuario::buscar("token", $token);

        if(empty($usuario)){
            Usuario::setAlerta("error", "Token No Válido");
            $mostrar = false;
        }

        if($_SERVER["REQUEST_METHOD"] === "POST"){
            // Añadir el Nuevo Password
            $usuario->sincronizar($_POST);

            // Validar el Password
            $alertas = $usuario->validarPassword();

            if(empty($alertas)){
                // Hashear el Nuevo Password
                $usuario->hashPassword();

                // Eliminar Token
                $usuario->token = "";
                //unset($usuario->password2);

                // Guardar Usuario en la BD
                $resultado = $usuario->guardar();

                // Redireccionar
                if($resultado){
                    header("Location: /");
                }
            }
        }

        $alertas = Usuario::getAlertas();

        // Render a la vista
        $router->render("auth/reestablecer", [
            "titulo" => "Reestablecer Password",
            "alertas" => $alertas,
            "mostrar" => $mostrar
        ]);
    }

    public static function mensaje(Router $router){
        
        $router->render("auth/mensaje", [
            "titulo" => "Cuenta Creada Exitosamente"
        ]);
    }

    public static function confirmar(Router $router){
        
        $token = s($_GET["token"]);

        if(!$token)
            header("Location: /");
        
        // Encontrar al Usuario con el Token
        $usuario = Usuario::buscar("token", $token);

        if(empty($usuario))
            Usuario::setAlerta("error", "Token No Válido");
        else{
            $usuario->confirmado = 1;
            $usuario->token = "";
            unset($usuario->password2);
            $usuario->guardar();

            Usuario::setAlerta("exito", "Cuenta Confirmada Correctamente");
        }

        $alertas = Usuario::getAlertas();

        $router->render("auth/confirmar", [
            "titulo" => "Confirma tu Cuenta",
            "alertas" => $alertas
        ]);
    }
}