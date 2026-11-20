<?php

session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.html");
    exit;
}

$rol = $_SESSION['rol'] ?? 'estudiante';

$puedeCrearProyecto = (
    $rol === 'docente' ||
    $rol === 'administrador'
);

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>AulaViva — Monitoreo en vivo</title>

    <link
        href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css"
        rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="styles.css">

</head>

<body>


<!-- =====================================================
     NAVBAR
===================================================== -->

<nav class="navbar navbar-custom container-fluid px-4 px-lg-5">

    <div class="d-flex align-items-center gap-3">

        <div class="brand-logos">

            <div class="logo-chip school">
                T
            </div>

            <div class="logo-chip project">
                4
            </div>

        </div>

        <a
            class="navbar-brand brand mb-0"
            href="home.html">

            Tecnica<span class="accent">N4</span>

        </a>

    </div>


    <div class="d-none d-lg-flex align-items-center gap-4">

        <a
            href="home.html"
            class="nav-link-custom text-decoration-none">

            Inicio

        </a>

        <a
            href="#sensores"
            class="nav-link-custom text-decoration-none">

            Sensores

        </a>

        <?php if ($puedeCrearProyecto): ?>

            <a
                href="#crear-proyecto"
                class="nav-link-custom text-decoration-none">

                Crear proyecto

            </a>

        <?php endif; ?>

        <a
            href="#proyectos"
            class="nav-link-custom text-decoration-none">

            Proyectos

        </a>

        <?php if ($rol === 'administrador'): ?>

            <a
                href="admin/admin.html"
                class="nav-link-custom text-decoration-none">

                Panel de administrador

            </a>

        <?php endif; ?>

    </div>


    <div class="d-flex align-items-center gap-2">

        <span
            id="rolUsuario"
            class="role-badge">

            <?= htmlspecialchars($rol) ?>

        </span>

        <?php if ($puedeCrearProyecto): ?>

            <button
                id="btnCrearProyectoNav"
                type="button"
                class="btn btn-verde btn-sm"
                onclick="abrirCrearProyecto()">

                + Crear proyecto

            </button>

        <?php endif; ?>

    </div>

</nav>



<!-- =====================================================
     DASHBOARD
===================================================== -->

<main class="container dashboard">


    <!-- =================================================
         ENCABEZADO
    ================================================== -->

    <div class="dashboard-header">

        <div>

            <div class="eyebrow">

                <span class="pulse-dot"></span>

                Datos en tiempo real

            </div>

            <h1 class="dashboard-title">
                Monitoreo en vivo
            </h1>

            <p class="dashboard-subtitle">

                Datos recibidos directamente desde
                los sensores conectados.

            </p>

        </div>


        <div
            id="estadoApi"
            class="live-status">

            <span class="pulse-dot"></span>

            <div>

                <strong>
                    Esperando conexión
                </strong>

                <small>
                    API de sensores
                </small>

            </div>

        </div>

    </div>



    <!-- =================================================
         SENSORES
    ================================================== -->

    <section id="sensores">

        <div class="row g-4">


            <!-- MQ5 -->

            <div class="col-md-4">

                <article class="sensor-card">

                    <div class="sensor-card-top">

                        <div class="sensor-icon">
                            🔥
                        </div>

                        <span class="sensor-status">
                            Esperando
                        </span>

                    </div>

                    <div class="sensor-info">

                        <p class="sensor-label">
                            SENSOR MQ-5
                        </p>

                        <h2
                            id="mq5Valor"
                            class="sensor-value">

                            —

                        </h2>

                        <p class="sensor-name">
                            Detección de gases combustibles
                        </p>

                    </div>

                    <div class="sensor-footer">

                        <span>
                            Última lectura
                        </span>

                        <strong id="mq5Hora">
                            —
                        </strong>

                    </div>

                </article>

            </div>



            <!-- DHT11 -->

            <div class="col-md-4">

                <article class="sensor-card">

                    <div class="sensor-card-top">

                        <div class="sensor-icon">
                            🌡️
                        </div>

                        <span class="sensor-status">
                            Esperando
                        </span>

                    </div>

                    <div class="sensor-info">

                        <p class="sensor-label">
                            SENSOR DHT11
                        </p>

                        <div class="sensor-measure">

                            <span class="measure-label">
                                Temperatura
                            </span>

                            <h2
                                id="dht11Temperatura"
                                class="sensor-value sensor-value-small">

                                —

                                <span>°C</span>

                            </h2>

                        </div>

                        <div class="sensor-measure">

                            <span class="measure-label">
                                Humedad
                            </span>

                            <h2
                                id="dht11Humedad"
                                class="sensor-value sensor-value-small">

                                —

                                <span>%</span>

                            </h2>

                        </div>

                    </div>

                    <div class="sensor-footer">

                        <span>
                            Última lectura
                        </span>

                        <strong id="dht11Hora">
                            —
                        </strong>

                    </div>

                </article>

            </div>



            <!-- DS18B20 -->

            <div class="col-md-4">

                <article class="sensor-card">

                    <div class="sensor-card-top">

                        <div class="sensor-icon">
                            🌡️
                        </div>

                        <span class="sensor-status">
                            Esperando
                        </span>

                    </div>

                    <div class="sensor-info">

                        <p class="sensor-label">
                            SENSOR DS18B20
                        </p>

                        <h2
                            id="ds18b20Temperatura"
                            class="sensor-value">

                            —

                            <span>°C</span>

                        </h2>

                        <p class="sensor-name">
                            Temperatura de precisión
                        </p>

                    </div>

                    <div class="sensor-footer">

                        <span>
                            Última lectura
                        </span>

                        <strong id="ds18b20Hora">
                            —
                        </strong>

                    </div>

                </article>

            </div>

        </div>

    </section>



    <!-- =================================================
         INFORMACIÓN
    ================================================== -->

    <section class="dashboard-info">

        <div class="info-box">

            <div class="info-icon">
                ✓
            </div>

            <div>

                <strong id="mensajeEstado">
                    Esperando datos de los sensores
                </strong>

                <p>
                    Los valores aparecerán cuando
                    la API esté disponible.
                </p>

            </div>

        </div>


        <div class="last-update">

            <span>
                Última actualización
            </span>

            <strong id="ultimaActualizacion">
                —
            </strong>

        </div>

    </section>



    <!-- =================================================
         CREAR PROYECTO
    ================================================== -->

    <?php if ($puedeCrearProyecto): ?>

        <section
            id="crear-proyecto"
            class="create-project-section">

            <div>

                <div class="eyebrow">
                    Guardar mediciones
                </div>

                <h2>
                    ¿Querés guardar estos datos?
                </h2>

                <p>
                    Creá un proyecto para comenzar a registrar
                    las mediciones de los sensores.
                </p>

            </div>

            <button
                id="btnCrearProyecto"
                type="button"
                class="btn btn-verde"
                onclick="abrirCrearProyecto()">

                + Crear proyecto

            </button>

        </section>

    <?php endif; ?>



    <!-- =================================================
         PROYECTOS
    ================================================== -->

    <section
        id="proyectos"
        class="projects-section">

        <div class="projects-heading">

            <div>

                <div class="eyebrow">
                    Monitoreos
                </div>

                <h2>
                    Proyectos actuales
                </h2>

                <p>
                    Proyectos creados y monitoreos disponibles.
                </p>

            </div>

        </div>


        <div
            id="listaProyectos"
            class="row g-4">

        </div>


        <div
            id="sinProyectos"
            class="empty-projects"
            style="display:none;">

            <div class="empty-projects-icon">
                📊
            </div>

            <h3>
                Todavía no hay proyectos
            </h3>

            <p>
                Cuando un profesor cree un proyecto,
                aparecerá acá.
            </p>

        </div>

    </section>

</main>



<!-- =====================================================
     MODAL CREAR PROYECTO
===================================================== -->

<?php if ($puedeCrearProyecto): ?>

<div
    class="modal fade"
    id="modalProyecto"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content project-modal">


            <!-- HEADER -->

            <div class="modal-header">

                <div>

                    <div class="eyebrow">
                        Nuevo proyecto
                    </div>

                    <h3>
                        Crear monitoreo
                    </h3>

                </div>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Cerrar">
                </button>

            </div>



            <!-- BODY -->

            <div class="modal-body">

                <form
                    id="formProyecto"
                    onsubmit="event.preventDefault(); crearProyecto();">


                    <!-- TÍTULO -->

                    <div class="mb-3">

                        <label
                            for="tituloProyecto"
                            class="form-label">

                            Título del proyecto

                        </label>

                        <input
                            id="tituloProyecto"
                            name="titulo"
                            type="text"
                            class="form-control"
                            placeholder="Ej: Monitoreo ambiental"
                            maxlength="200"
                            autocomplete="off"
                            required>

                    </div>



                    <!-- DESCRIPCIÓN -->

                    <div class="mb-3">

                        <label
                            for="descripcionProyecto"
                            class="form-label">

                            Descripción

                        </label>

                        <textarea
                            id="descripcionProyecto"
                            name="descripcion"
                            class="form-control"
                            rows="3"
                            placeholder="Descripción del proyecto"></textarea>

                    </div>



                    <!-- CURSO -->

                    <div class="mb-3">

                        <label
                            for="cursoProyecto"
                            class="form-label">

                            Curso

                        </label>

                        <input
                            id="cursoProyecto"
                            name="curso"
                            type="text"
                            class="form-control"
                            placeholder="Ej: 4° 1°"
                            maxlength="100"
                            autocomplete="off"
                            required>

                    </div>



                    <!-- MATERIA -->

                    <div class="mb-3">

                        <label
                            for="materiaProyecto"
                            class="form-label">

                            Materia

                        </label>

                        <input
                            id="materiaProyecto"
                            name="materia"
                            type="text"
                            class="form-control"
                            placeholder="Ej: Electrónica"
                            maxlength="100"
                            autocomplete="off"
                            required>

                    </div>



                    <!-- RESPONSABLE -->

                    <div class="mb-3">

                        <label
                            for="responsableProyecto"
                            class="form-label">

                            Responsable

                        </label>

                        <input
                            id="responsableProyecto"
                            name="responsable"
                            type="text"
                            class="form-control"
                            placeholder="Nombre del responsable"
                            maxlength="150"
                            autocomplete="off">

                    </div>



                    <!-- FECHA INICIO -->

                    <div class="mb-3">

                        <label
                            for="fechaInicioProyecto"
                            class="form-label">

                            Fecha de inicio

                        </label>

                        <input
                            type="date"
                            id="fechaInicioProyecto"
                            name="fecha_inicio"
                            class="form-control"
                            required>

                    </div>



                    <!-- FECHA FIN -->

                    <div class="mb-3">

                        <label
                            for="fechaFinProyecto"
                            class="form-label">

                            Fecha de finalización

                        </label>

                        <input
                            type="date"
                            id="fechaFinProyecto"
                            name="fecha_fin"
                            class="form-control"
                            required>

                    </div>



                    <!-- INFORMACIÓN -->

                    <div class="project-modal-info">

                        <strong>
                            Período de monitoreo
                        </strong>

                        <p>
                            Seleccioná desde qué fecha hasta qué
                            fecha querés que dure el proyecto.
                            Las mediciones se guardarán durante
                            ese período.
                        </p>

                    </div>



                    <!-- BOTÓN -->

                    <button
                        type="submit"
                        id="btnGuardarProyecto"
                        class="btn btn-verde w-100 mt-3">

                        Crear proyecto

                    </button>

                </form>

            </div>

        </div>

    </div>

</div>

<?php endif; ?>



<!-- =====================================================
     FOOTER
===================================================== -->

<footer class="footer dashboard-footer">

    <div class="container">

        <div class="footer-bottom">

            <span>
                © 2026 Tecnica 4
            </span>

            <span>
                AulaViva · Monitoreo en tiempo real
            </span>

        </div>

    </div>

</footer>



<script
    src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js">
</script>

<script src="app.js?v=9999"></script>

</body>

</html>
