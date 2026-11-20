// =====================================================
// AULAVIVA - APP.JS
// =====================================================

let datosSensoresActuales = null;
let proyectoActivo = null;
let ultimoIntentoGuardado = 0;


// =====================================================
// API DE SENSORES
// =====================================================

async function obtenerDatosSensores() {

    try {

        const respuesta = await fetch(
            "sensores.php?t=" + Date.now()
        );

        if (!respuesta.ok) {
            throw new Error("HTTP " + respuesta.status);
        }

        const datos = await respuesta.json();

        if (!datos.ok) {
            throw new Error(
                datos.mensaje || "API sin datos"
            );
        }

        datosSensoresActuales = datos;

        mostrarSensores(datos);

        // Guardar aproximadamente una medición por hora
        const ahora = Date.now();

        if (
            ahora - ultimoIntentoGuardado >=
            60 * 60 * 1000
        ) {

            ultimoIntentoGuardado = ahora;

            await guardarMedicion();
        }

    } catch (error) {

        console.error(
            "Error API sensores:",
            error
        );

        mostrarAPIDesconectada();
    }
}


// =====================================================
// API DESCONECTADA
// =====================================================

function mostrarAPIDesconectada() {

    const estado =
        document.getElementById("estadoApi");

    if (estado) {

        estado.innerHTML = `
            <span class="pulse-dot"></span>

            <div>
                <strong>API desconectada</strong>

                <small>
                    No se pudieron recibir datos
                </small>
            </div>
        `;
    }


    const mensaje =
        document.getElementById("mensajeEstado");

    if (mensaje) {

        mensaje.textContent =
            "Esperando datos de los sensores";
    }
}


// =====================================================
// MOSTRAR SENSORES
// =====================================================

function mostrarSensores(datos) {

    if (!datos || !datos.sensores) {
        return;
    }

    const sensores = datos.sensores;


    // -------------------------------------------------
    // MQ5
    // -------------------------------------------------

    if (sensores.mq5) {

        const valor =
            document.getElementById("mq5Valor");

        const hora =
            document.getElementById("mq5Hora");


        if (valor) {

            valor.textContent =
                sensores.mq5.valor +
                " " +
                (sensores.mq5.unidad || "");
        }


        if (hora) {

            hora.textContent =
                sensores.mq5.hora || "—";
        }
    }


    // -------------------------------------------------
    // DHT11
    // -------------------------------------------------

    if (sensores.dht11) {

        const temperatura =
            document.getElementById(
                "dht11Temperatura"
            );

        const humedad =
            document.getElementById(
                "dht11Humedad"
            );

        const hora =
            document.getElementById(
                "dht11Hora"
            );


        if (temperatura) {

            temperatura.innerHTML =
                sensores.dht11.temperatura +
                " <span>°C</span>";
        }


        if (humedad) {

            humedad.innerHTML =
                sensores.dht11.humedad +
                " <span>%</span>";
        }


        if (hora) {

            hora.textContent =
                sensores.dht11.hora || "—";
        }
    }


    // -------------------------------------------------
    // DS18B20
    // -------------------------------------------------

    if (sensores.ds18b20) {

        const temperatura =
            document.getElementById(
                "ds18b20Temperatura"
            );

        const hora =
            document.getElementById(
                "ds18b20Hora"
            );


        if (temperatura) {

            temperatura.innerHTML =
                sensores.ds18b20.temperatura +
                " <span>°C</span>";
        }


        if (hora) {

            hora.textContent =
                sensores.ds18b20.hora || "—";
        }
    }


    // -------------------------------------------------
    // ESTADO API
    // -------------------------------------------------

    const estado =
        document.getElementById("estadoApi");

    if (estado) {

        estado.innerHTML = `
            <span class="pulse-dot"></span>

            <div>
                <strong>API conectada</strong>

                <small>
                    Datos recibidos correctamente
                </small>
            </div>
        `;
    }


    const mensaje =
        document.getElementById("mensajeEstado");

    if (mensaje) {

        mensaje.textContent =
            datos.mensaje ||
            "Datos recibidos correctamente";
    }


    const ultima =
        document.getElementById(
            "ultimaActualizacion"
        );

    if (ultima) {

        ultima.textContent =
            new Date().toLocaleTimeString(
                "es-AR"
            );
    }


    document
        .querySelectorAll(".sensor-status")
        .forEach(function (elemento) {

            elemento.textContent = "Activo";

        });
}


// =====================================================
// ABRIR MODAL
// =====================================================

function abrirCrearProyecto() {

    const modalElemento =
        document.getElementById(
            "modalProyecto"
        );

    if (!modalElemento) {

        alert(
            "No se encontró el modal de proyecto."
        );

        return;
    }


    if (
        typeof bootstrap === "undefined" ||
        !bootstrap.Modal
    ) {

        alert(
            "Bootstrap no se cargó correctamente."
        );

        return;
    }


    const modal =
        bootstrap.Modal.getOrCreateInstance(
            modalElemento
        );

    modal.show();
}


// =====================================================
// CERRAR MODAL
// =====================================================

function cerrarModalProyecto() {

    const modalElemento =
        document.getElementById(
            "modalProyecto"
        );

    if (!modalElemento) {
        return;
    }


    if (
        typeof bootstrap === "undefined" ||
        !bootstrap.Modal
    ) {
        return;
    }


    const modal =
        bootstrap.Modal.getInstance(
            modalElemento
        );


    if (modal) {
        modal.hide();
    }
}


// =====================================================
// OBTENER FECHA DE HOY
// =====================================================

function obtenerFechaHoy() {

    const ahora = new Date();

    const año =
        ahora.getFullYear();

    const mes =
        String(
            ahora.getMonth() + 1
        ).padStart(2, "0");

    const dia =
        String(
            ahora.getDate()
        ).padStart(2, "0");

    return (
        año +
        "-" +
        mes +
        "-" +
        dia
    );
}


// =====================================================
// CREAR PROYECTO
// =====================================================

async function crearProyecto() {

    console.log(
        "CREAR PROYECTO: INICIO"
    );


    // =================================================
    // OBTENER LOS CAMPOS
    // =================================================

    const titulo =
        document.getElementById(
            "tituloProyecto"
        );

    const descripcion =
        document.getElementById(
            "descripcionProyecto"
        );

    const fechaInicio =
        document.getElementById(
            "fechaInicioProyecto"
        );

    const fechaFin =
        document.getElementById(
            "fechaFinProyecto"
        );


    // =================================================
    // COMPROBACIÓN
    // =================================================

    console.log({

        titulo: !!titulo,

        descripcion: !!descripcion,

        fechaInicio: !!fechaInicio,

        fechaFin: !!fechaFin

    });


    if (!titulo) {

        alert(
            "No se encontró el campo Título del proyecto."
        );

        return;
    }


    if (!descripcion) {

        alert(
            "No se encontró el campo Descripción."
        );

        return;
    }


    if (!fechaInicio) {

        alert(
            "No se encontró el campo Fecha de inicio."
        );

        return;
    }


    if (!fechaFin) {

        alert(
            "No se encontró el campo Fecha de finalización."
        );

        return;
    }


    // =================================================
    // OBTENER VALORES
    // =================================================

    const tituloValor =
        titulo.value.trim();

    const descripcionValor =
        descripcion.value.trim();

    const fechaInicioValor =
        fechaInicio.value;

    const fechaFinValor =
        fechaFin.value;


    console.log(
        "DATOS:",
        {
            titulo: tituloValor,
            descripcion: descripcionValor,
            fecha_inicio: fechaInicioValor,
            fecha_fin: fechaFinValor
        }
    );


    // =================================================
    // VALIDAR TÍTULO
    // =================================================

    if (tituloValor === "") {

        alert(
            "Ingresá un título para el proyecto."
        );

        titulo.focus();

        return;
    }


    // =================================================
    // VALIDAR FECHA DE INICIO
    // =================================================

    if (fechaInicioValor === "") {

        alert(
            "Seleccioná la fecha de inicio."
        );

        fechaInicio.focus();

        return;
    }


    // =================================================
    // VALIDAR QUE EL INICIO NO SEA ANTERIOR A HOY
    // =================================================

    const fechaHoy =
        obtenerFechaHoy();


    if (fechaInicioValor < fechaHoy) {

        alert(
            "Fecha inválida: el proyecto no puede comenzar antes de hoy."
        );

        fechaInicio.focus();

        return;
    }


    // =================================================
    // VALIDAR FECHA DE FIN
    // =================================================

    if (fechaFinValor === "") {

        alert(
            "Seleccioná la fecha de finalización."
        );

        fechaFin.focus();

        return;
    }


    // =================================================
    // VALIDAR FECHA DE FIN CONTRA INICIO
    // =================================================

    if (fechaFinValor < fechaInicioValor) {

        alert(
            "Fecha inválida: la fecha de finalización no puede ser anterior a la fecha de inicio."
        );

        fechaFin.focus();

        return;
    }


    // =================================================
    // BOTÓN
    // =================================================

    const boton =
        document.getElementById(
            "btnGuardarProyecto"
        );


    if (boton) {

        boton.disabled = true;

        boton.textContent =
            "Creando proyecto...";
    }


    // =================================================
    // DATOS A ENVIAR
    // =================================================

    const datosProyecto = {

        titulo:
            tituloValor,

        descripcion:
            descripcionValor,

        fecha_inicio:
            fechaInicioValor,

        fecha_fin:
            fechaFinValor
    };


    console.log(
        "ENVIANDO:",
        datosProyecto
    );


    // =================================================
    // FETCH
    // =================================================

    try {

        const respuesta =
            await fetch(
                "proyectos.php?accion=crear",
                {

                    method: "POST",

                    headers: {

                        "Content-Type":
                            "application/json",

                        "Accept":
                            "application/json"
                    },

                    body:
                        JSON.stringify(
                            datosProyecto
                        )
                }
            );


        console.log(
            "HTTP STATUS:",
            respuesta.status
        );


        // -------------------------------------------------
        // LEER COMO TEXTO PRIMERO
        // -------------------------------------------------

        const texto =
            await respuesta.text();


        console.log(
            "RESPUESTA DEL SERVIDOR:",
            texto
        );


        // -------------------------------------------------
        // INTENTAR JSON
        // -------------------------------------------------

        let resultado;


        try {

            resultado =
                JSON.parse(texto);

        } catch (error) {

            console.error(
                "El servidor no devolvió JSON:",
                error
            );


            alert(
                "El servidor respondió algo que no es JSON.\n\n" +
                texto.substring(0, 500)
            );

            return;
        }


        // =================================================
        // RESPUESTA PHP
        // =================================================

        if (
            !resultado ||
            resultado.ok !== true
        ) {

            alert(
                resultado?.mensaje ||
                "El servidor no pudo crear el proyecto."
            );

            return;
        }


        // =================================================
        // PROYECTO CREADO
        // =================================================

        proyectoActivo = {

            id:
                resultado.id,

            titulo:
                tituloValor,

            descripcion:
                descripcionValor,

            fecha_inicio:
                fechaInicioValor,

            fecha_fin:
                fechaFinValor
        };


        console.log(
            "PROYECTO CREADO:",
            proyectoActivo
        );


        // =================================================
        // LIMPIAR FORMULARIO
        // =================================================

        titulo.value = "";

        descripcion.value = "";

        fechaInicio.value = "";

        fechaFin.value = "";


        // =================================================
        // CERRAR MODAL
        // =================================================

        cerrarModalProyecto();


        // =================================================
        // ACTUALIZAR LISTA
        // =================================================

        await cargarProyectos();


        // =================================================
        // GUARDAR MEDICIÓN
        // =================================================

        if (datosSensoresActuales) {

            await guardarMedicion();
        }


        // =================================================
        // MENSAJE
        // =================================================

        alert(
            "Proyecto creado correctamente."
        );


    } catch (error) {

        console.error(
            "ERROR CREANDO PROYECTO:",
            error
        );


        alert(
            "No se pudo conectar con el servidor.\n\n" +
            error.message
        );


    } finally {

        if (boton) {

            boton.disabled = false;

            boton.textContent =
                "Crear proyecto";
        }
    }
}


// =====================================================
// OBTENER PROYECTO ACTIVO
// =====================================================

async function obtenerProyectoActivo() {

    if (proyectoActivo) {
        return proyectoActivo;
    }


    try {

        const respuesta =
            await fetch(
                "proyectos.php?accion=listar&t=" +
                Date.now()
            );


        if (!respuesta.ok) {

            throw new Error(
                "HTTP " +
                respuesta.status
            );
        }


        const datos =
            await respuesta.json();


        if (
            !datos.ok ||
            !Array.isArray(datos.proyectos) ||
            datos.proyectos.length === 0
        ) {

            return null;
        }


        const ahora =
            new Date();


        const activo =
            datos.proyectos.find(
                function (proyecto) {

                    const inicio =
                        new Date(
                            proyecto.fecha_inicio +
                            "T00:00:00"
                        );


                    const fin =
                        new Date(
                            proyecto.fecha_fin +
                            "T23:59:59"
                        );


                    return (
                        ahora >= inicio &&
                        ahora <= fin
                    );
                }
            );


        proyectoActivo =
            activo ||
            datos.proyectos[0];


        return proyectoActivo;


    } catch (error) {

        console.error(
            "Error buscando proyecto:",
            error
        );

        return null;
    }
}


// =====================================================
// GUARDAR MEDICIÓN
// =====================================================

async function guardarMedicion() {

    if (!datosSensoresActuales) {

        console.log(
            "No hay datos de sensores."
        );

        return;
    }


    const proyecto =
        await obtenerProyectoActivo();


    if (!proyecto) {

        console.log(
            "No hay proyecto activo."
        );

        return;
    }


    const sensores =
        datosSensoresActuales.sensores;


    if (!sensores) {
        return;
    }


    const datos = {

        id_proyecto:
            Number(proyecto.id),

        mq5:
            sensores.mq5
                ? Number(
                    sensores.mq5.valor
                )
                : null,

        dht11_temperatura:
            sensores.dht11
                ? Number(
                    sensores.dht11.temperatura
                )
                : null,

        dht11_humedad:
            sensores.dht11
                ? Number(
                    sensores.dht11.humedad
                )
                : null,

        ds18b20:
            sensores.ds18b20
                ? Number(
                    sensores.ds18b20.temperatura
                )
                : null
    };


    console.log(
        "ENVIANDO MEDICIÓN:",
        datos
    );


    try {

        const respuesta =
            await fetch(
                "proyectos.php?accion=guardar_medicion",
                {

                    method: "POST",

                    headers: {

                        "Content-Type":
                            "application/json",

                        "Accept":
                            "application/json"
                    },

                    body:
                        JSON.stringify(datos)
                }
            );


        const texto =
            await respuesta.text();


        console.log(
            "RESPUESTA MEDICIÓN:",
            texto
        );


    } catch (error) {

        console.error(
            "Error guardando medición:",
            error
        );
    }
}


// =====================================================
// CARGAR PROYECTOS
// =====================================================

async function cargarProyectos() {

    const contenedor =
        document.getElementById(
            "listaProyectos"
        );

    const sinProyectos =
        document.getElementById(
            "sinProyectos"
        );


    if (!contenedor) {
        return;
    }


    try {

        const respuesta =
            await fetch(
                "proyectos.php?accion=listar&t=" +
                Date.now()
            );


        if (!respuesta.ok) {

            throw new Error(
                "HTTP " +
                respuesta.status
            );
        }


        const datos =
            await respuesta.json();


        if (
            !datos.ok ||
            !Array.isArray(datos.proyectos) ||
            datos.proyectos.length === 0
        ) {

            contenedor.innerHTML = "";


            if (sinProyectos) {

                sinProyectos.style.display =
                    "block";
            }


            return;
        }


        if (sinProyectos) {

            sinProyectos.style.display =
                "none";
        }


        contenedor.innerHTML = "";


        datos.proyectos.forEach(
            function (proyecto) {

                const tarjeta =
                    document.createElement(
                        "div"
                    );


                tarjeta.className =
                    "col-md-6 col-lg-4";


                tarjeta.innerHTML = `

                    <article class="sensor-card">

                        <div class="sensor-card-top">

                            <div class="sensor-icon">
                                📊
                            </div>

                            <span class="sensor-status">
                                Proyecto
                            </span>

                        </div>


                        <div class="sensor-info">

                            <p class="sensor-label">
                                PROYECTO
                            </p>

                            <h3>
                                ${escapeHTML(
                                    proyecto.titulo
                                )}
                            </h3>

                            <p class="sensor-name">
                                ${escapeHTML(
                                    proyecto.descripcion || ""
                                )}
                            </p>

                        </div>


                        <div class="sensor-footer">

                            <span>
                                ${formatearFecha(
                                    proyecto.fecha_inicio
                                )}
                                →
                                ${formatearFecha(
                                    proyecto.fecha_fin
                                )}
                            </span>

                            <a
                                href="proyecto.html?id=${encodeURIComponent(
                                    proyecto.id
                                )}"
                                class="btn btn-verde btn-sm"
                            >
                                Ver proyecto
                            </a>

                        </div>

                    </article>

                `;


                contenedor.appendChild(
                    tarjeta
                );
            }
        );


    } catch (error) {

        console.error(
            "Error cargando proyectos:",
            error
        );
    }
}


// =====================================================
// FORMATEAR FECHA
// =====================================================

function formatearFecha(fecha) {

    if (!fecha) {
        return "—";
    }


    const partes =
        String(fecha).split("-");


    if (partes.length === 3) {

        return (
            partes[2] +
            "/" +
            partes[1] +
            "/" +
            partes[0]
        );
    }


    return fecha;
}


// =====================================================
// SEGURIDAD HTML
// =====================================================

function escapeHTML(texto) {

    const elemento =
        document.createElement(
            "div"
        );


    elemento.textContent =
        texto ?? "";


    return elemento.innerHTML;
}


// =====================================================
// INICIALIZACIÓN
// =====================================================

document.addEventListener(
    "DOMContentLoaded",
    function () {

        console.log(
            "================================="
        );

        console.log(
            "AULAVIVA APP.JS CARGADO"
        );

        console.log(
            "VERSIÓN: 10000"
        );

        console.log(
            "================================="
        );


        // Cargar proyectos
        cargarProyectos();


        // Obtener sensores
        obtenerDatosSensores();


        // Actualizar sensores cada 10 segundos
        setInterval(
            obtenerDatosSensores,
            10000
        );

    }
);
