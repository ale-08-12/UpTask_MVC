<?php include_once __DIR__ . "/header-dashboard.php"; ?>

<div class="contenedor-sm">
    <?php include_once __DIR__ . "/../templates/alertas.php";?>

    <a class="enlace" href="/cambiar-password">Cambiar Password</a>

    <form class="formulario" method="POST" action="/perfil">
        <div class="campo">
            <label for="nombre">Nombre</label>
            <input type="text" name="nombre" placeholder="Tu Nombre" value="<?php echo $usuario->nombre; ?>">
        </div>

        <div class="campo">
            <label for="email">Email</label>
            <input type="email" name="email" placeholder="Tu Email" value="<?php echo $usuario->email; ?>">
        </div>

        <input type="submit" value="Guardar Cambios">
    </form>
</div>

<?php include_once __DIR__ . "/footer-dashboard.php"; ?>