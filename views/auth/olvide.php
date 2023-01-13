<div class="contenedor olvide">
    <?php include_once __DIR__ . "/../templates/nombre-sitio.php";?>

    <div class="contenedor-sm">
        <p class="descripcion-pagina">Recupera tu Cuenta</p>

        <?php include_once __DIR__ . "/../templates/alertas.php";?>

        <form class="formulario" method="POST" action="/olvide" novalidate>

            <div class="campo">
                <label for="email">Email</label>
                <input type="email" id="email" placeholder="Tu Email" name="email">
            </div>

            <input class="boton" type="submit" value="Enviar Instrucciones">
        </form>

        <div class="acciones">
            <a href="/">¿Ya tienes cuenta? Iniciar Sesión</a>
            <a href="/crear">¿Aún no tienes una cuenta? Obtener una</a>
        </div>
    </div> <!-- .contenedor-sm -->
</div>