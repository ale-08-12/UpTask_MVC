<?php

namespace Models;

class Usuario extends ActiveRecord{
    protected static $tabla = "usuarios";
    protected static $columnasDB = ["id", "nombre", "email", "password", "token", "confirmado"];

/*
    public $id;
    public $nombre;
    public $email;
    public $password;
    public $token;
    public $confirmado;
*/

    public function __construct($args = []){
        $this->id = $args["id"] ?? null;
        $this->nombre = $args["nombre"] ?? "";
        $this->email = $args["email"] ?? "";
        $this->password = $args["password"] ?? "";
        $this->password2 = $args["password2"] ?? "";
        $this->password_actual = $args["password_actual"] ?? "";
        $this->password_nuevo = $args["password_nuevo"] ?? "";
        $this->token = $args["token"] ?? "";
        $this->confirmado = $args["confirmado"] ?? "0";
    }
    
    // Hashea el password
    public function hashPassword(){
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    // Generar un Token
    public function crearToken(){
        $this->token = md5(uniqid());
    }

    public function comprobar_password(){
        return password_verify($this->password_actual, $this->password);
    }

    // Validar el Login de Usuarios
    public function validarLogin(){
        if(!$this->email){
            self::$alertas["error"][] = "El Email es Obligatorio";
        }

        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)){
            self::$alertas["error"][] = "Email no Válido";
        }

        if(!$this->password){
            self::$alertas["error"][] = "El Password no puede ir Vacio";
        }

        return self::$alertas;
    }

    // Valida un Email
    public function validarEmail(){
        if(!$this->email){
            self::$alertas["error"][] = "El Email es Obligatorio";
        }

        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)){
            self::$alertas["error"][] = "Email no Válido";
        }

        return self::$alertas;
    }

    // Valida el Password
    public function validarPassword(){
        if(!$this->password){
            self::$alertas["error"][] = "El Password no puede ir Vacio";
        }
        
        if(strlen($this->password) < 6){
            self::$alertas["error"][] = "El Password debe contener al menos 6 caracteres";
        }

        if($this->password != $this->password2){
            self::$alertas["error"][] = "Los Passwords son Diferentes";
        }

        return self::$alertas;
    }

    // Validacion para cuentas nuevas
    public function validarNuevaCuenta(){
        if(!$this->nombre){
            self::$alertas["error"][] = "El Nombre del Usuario es Obligatorio";
        }

        if(!$this->email){
            self::$alertas["error"][] = "El Email del Usuario es Obligatorio";
        }

        if(!$this->password){
            self::$alertas["error"][] = "El Password no puede ir Vacio";
        }

        if(strlen($this->password) < 6){
            self::$alertas["error"][] = "El Password debe contener al menos 6 caracteres";
        }

        if($this->password != $this->password2){
            self::$alertas["error"][] = "Los Passwords son Diferentes";
        }

        return self::$alertas;
    }

    function validar_perfil(){
        if(!$this->nombre){
            self::$alertas["error"][] = "El Nombre del Usuario es Obligatorio";
        }

        if(!$this->email){
            self::$alertas["error"][] = "El Email del Usuario es Obligatorio";
        }
    }

    public function nuevo_password(){
        if(!$this->password_actual){
            self::$alertas["error"][] = "El Password Actual no puede ir Vacio";
        }

        if(strlen($this->password_actual) < 6){
            self::$alertas["error"][] = "El Password Actual debe contener al menos 6 caracteres";
        }

        if(!$this->password_nuevo){
            self::$alertas["error"][] = "El Password Nuevo no puede ir Vacio";
        }

        if(strlen($this->password_nuevo) < 6){
            self::$alertas["error"][] = "El Password Nuevo debe contener al menos 6 caracteres";
        }

        return self::$alertas;
    }
}