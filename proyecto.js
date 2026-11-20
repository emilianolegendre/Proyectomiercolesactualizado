// =====================================================
// AULAVIVA - PROYECTO.JS
// =====================================================

let idProyecto = null;


// =====================================================
// FORMATEAR FECHA
// =====================================================

function formatearFecha(fecha) {

    if (!fecha) {
        return "—";
    }

    const texto = String(fecha);

    const partes = texto.split(" ");

    const fechaParte = partes[0];

    const fechaArray = fechaParte.split("-");

    if (fechaArray.length === 3) {

        return (
            fechaArray[2] +
            "/" +
            fechaArray[1] +
            "/" +
            fechaArray[0]
        );
    }

    return texto;
}


// =====================================================
// FORMATEAR FECHA Y HORA
// =====================================================

function formatearFechaHora(fecha) {

    if (!fecha) {
        return "—";
    }

    const texto = String(fecha);

    const partes = texto.split(" ");

    if (partes.length < 2) {

        return formatearFecha(texto);
    }

    return (
        formatearFecha(partes[0]) +
        " " +
        partes[1]
    );
}


// =====================================================
// ESCAPAR HTML
// =====================================================

function escaparHTML(valor) {

    if (
        valor === null ||
        valor === undefined
    ) {
        return "";
    }

    return String(valor)

        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}


// =====================================================
// OBTENER ID DEL PROYECTO
// =====================================================

function obtenerIdProyecto() {

    const parametros =
        new URLSearchParams(
            window.location.search
        );

    const id =
        parametros.get("id");

    if (!id) {
        return null;
    }

    const numero =
        parseInt(id, 10);

    if (
        isNaN(numero) ||
        numero <= 0
    ) {
        return null;
    }

    return numero;
}


// =====================================================
// MOSTRAR ERROR
// =====================================================

function mostrarError(mensaje) {

    const cargando =
        document.getElementById(
            "cargandoProyecto"
        );

    const contenido =
        document.getElementById(
            "contenidoProyecto"
        );

    const error =
        document.getElementById(
            "errorProyecto"
        );

    const mensajeError =
        document.getElementById(
            "mensajeError"
        );

    if (cargando) {
        cargando.style.display = "none";
    }

    if (contenido) {
        contenido.style.display = "none";
    }

    if (error) {
        error.style.display = "block";
    }

    if (mensajeError) {
        mensajeError.textContent =
            mensaje || "Ocurrió un error.";
    }
}


// =====================================================
// CARGAR PROYECTO
// =====================================================

async function cargarProyecto() {

    idProyecto =
        obtenerIdProyecto();

    if (!idProyecto) {

        mostrarError(
            "No se encontró el ID del proyecto."
        );

        return;
    }

    try {

        const respuesta =
            await fetch(
                "proyectos.php?accion=obtener&id=" +
                encodeURIComponent(idProyecto) +
                "&t=" +
                Date.now()
            );

        if (!respuesta.ok) {

            throw new Error(
                "HTTP " +
                respuesta.status
            );
        }

        const texto =
            await respuesta.text();

        console.log(
            "RESPUESTA PROYECTO:",
            texto
        );

        let datos;

        try {

            datos =
                JSON.parse(texto);

        } catch (error) {

            console.error(
                "Respuesta no válida:",
                texto
            );

            mostrarError(
                "El servidor no devolvió una respuesta válida."
            );

            return;
        }

        if (
            !datos ||
            datos.ok !== true
        ) {

            mostrarError(
                datos?.mensaje ||
                "No se pudo cargar el proyecto."
            );

            return;
        }

        const proyecto =
            datos.proyecto;

        if (!proyecto) {

            mostrarError(
                "El proyecto no existe."
            );

            return;
        }


        // =================================================
        // ELEMENTOS DEL PROYECTO
        // =================================================

        const nombre =
            document.getElementById(
                "nombreProyecto"
            );

        const descripcion =
            document.getElementById(
                "descripcionProyecto"
            );

        const materia =
            document.getElementById(
                "materiaProyecto"
            );

        const curso =
            document.getElementById(
                "cursoProyecto"
            );

        const responsable =
            document.getElementById(
                "responsableProyecto"
            );

        const usuario =
            document.getElementById(
                "usuarioProyecto"
            );

        const estado =
            document.getElementById(
                "estadoProyecto"
            );


        // =================================================
        // TÍTULO
        // =================================================

        if (nombre) {

            nombre.textContent =
                proyecto.titulo ||
                "Sin título";
        }


        // =================================================
        // DESCRIPCIÓN
        // =================================================

        if (descripcion) {

            descripcion.textContent =
                proyecto.descripcion ||
                "Sin descripción";
        }


        // =================================================
        // MATERIA
        // =================================================

        if (materia) {

            materia.textContent =
                proyecto.Materia ||
                proyecto.materia ||
                "—";
        }


        // =================================================
        // CURSO
        // =================================================

        if (curso) {

            curso.textContent =
                proyecto.curso ||
                proyecto.Curso ||
                "—";
        }


        // =================================================
        // RESPONSABLE
        // =================================================

        if (responsable) {

            responsable.textContent =
                proyecto.Responsable ||
                proyecto.responsable ||
                "—";
        }


        // =================================================
        // USUARIO
        // =================================================

        if (usuario) {

            usuario.textContent =
                proyecto.Usuario ||
                proyecto.usuario ||
                "—";
        }


        // =================================================
        // ESTADO
        // =================================================

        if (estado) {

            estado.textContent =
                "Disponible";
        }


        // =================================================
        // MOSTRAR PROYECTO
        // =================================================

        const cargando =
            document.getElementById(
                "cargandoProyecto"
            );

        const contenido =
            document.getElementById(
                "contenidoProyecto"
            );

        if (cargando) {

            cargando.style.display =
                "none";
        }

        if (contenido) {

            contenido.style.display =
                "block";
        }


        // =================================================
        // CARGAR MEDICIONES
        // =================================================

        await cargarMediciones();

    } catch (error) {

        console.error(
            "Error cargando proyecto:",
            error
        );

        mostrarError(
            "No se pudo conectar con el servidor."
        );
    }
}


// =====================================================
// CARGAR MEDICIONES
// =====================================================

async function cargarMediciones() {

    if (!idProyecto) {
        return;
    }

    try {

        const respuesta =
            await fetch(
                "proyectos.php?accion=mediciones&id_proyecto=" +
                encodeURIComponent(idProyecto) +
                "&t=" +
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

        if (!datos.ok) {

            console.error(
                datos.mensaje
            );

            return;
        }

        mostrarMediciones(
            datos.mediciones || []
        );

    } catch (error) {

        console.error(
            "Error cargando mediciones:",
            error
        );
    }
}


// =====================================================
// AGRUPAR MEDICIONES
// =====================================================

function agruparMediciones(mediciones) {

    const grupos = {};

    mediciones.forEach(
        function (medicion) {

            const tiempo =
                medicion.fecha_hora;

            if (!tiempo) {
                return;
            }

            if (!grupos[tiempo]) {

                grupos[tiempo] = {

                    fecha_hora:
                        tiempo,

                    mq5:
                        null,

                    dht11_temperatura:
                        null,

                    dht11_humedad:
                        null,

                    ds18b20:
                        null
                };
            }

            const descripcion =
                String(
                    medicion.sensor_descripcion ||
                    ""
                ).toLowerCase();

            const valor =
                medicion.valor;


            // MQ5

            if (
                descripcion.includes("mq-5") ||
                descripcion.includes("mq5")
            ) {

                grupos[tiempo].mq5 =
                    valor;
            }


            // DHT11 TEMPERATURA

            else if (
                descripcion.includes("dht11") &&
                (
                    descripcion.includes("temperatura") ||
                    descripcion.includes("temp")
                )
            ) {

                grupos[tiempo]
                    .dht11_temperatura =
                    valor;
            }


            // DHT11 HUMEDAD

            else if (
                descripcion.includes("dht11") &&
                descripcion.includes("humedad")
            ) {

                grupos[tiempo]
                    .dht11_humedad =
                    valor;
            }


            // DS18B20

            else if (
                descripcion.includes("ds18b20")
            ) {

                grupos[tiempo].ds18b20 =
                    valor;
            }

        }
    );

    return Object.values(grupos)
        .sort(
            function (a, b) {

                return String(
                    b.fecha_hora
                ).localeCompare(
                    String(
                        a.fecha_hora
                    )
                );
            }
        );
}


// =====================================================
// FORMATEAR VALOR
// =====================================================

function formatearValor(
    valor,
    unidad
) {

    if (
        valor === null ||
        valor === undefined ||
        valor === ""
    ) {

        return "—";
    }

    const numero =
        Number(valor);

    if (isNaN(numero)) {

        return (
            String(valor) +
            (unidad || "")
        );
    }

    return (
        numero.toFixed(2) +
        (unidad || "")
    );
}


// =====================================================
// MOSTRAR MEDICIONES
// =====================================================

function mostrarMediciones(
    mediciones
) {

    const tabla =
        document.getElementById(
            "tablaMediciones"
        );

    const sinMediciones =
        document.getElementById(
            "sinMediciones"
        );

    const ultima =
        document.getElementById(
            "ultimaMedicion"
        );

    if (!tabla) {
        return;
    }

    const grupos =
        agruparMediciones(
            mediciones
        );

    tabla.innerHTML = "";


    // =================================================
    // SIN MEDICIONES
    // =================================================

    if (
        !grupos ||
        grupos.length === 0
    ) {

        if (sinMediciones) {

            sinMediciones.style.display =
                "block";
        }

        if (ultima) {

            ultima.textContent =
                "—";
        }

        actualizarValoresActuales(
            null
        );

        return;
    }


    if (sinMediciones) {

        sinMediciones.style.display =
            "none";
    }


    // =================================================
    // ÚLTIMA MEDICIÓN
    // =================================================

    const ultimaMedicion =
        grupos[0];

    if (ultima) {

        ultima.textContent =
            formatearFechaHora(
                ultimaMedicion.fecha_hora
            );
    }


    actualizarValoresActuales(
        ultimaMedicion
    );


    // =================================================
    // TABLA
    // =================================================

    grupos.forEach(
        function (medicion) {

            const fila =
                document.createElement(
                    "tr"
                );

            const textoFecha =
                String(
                    medicion.fecha_hora ||
                    ""
                );

            const partes =
                textoFecha.split(" ");

            const fecha =
                partes[0]
                    ? formatearFecha(
                        partes[0]
                    )
                    : "—";

            const hora =
                partes[1] ||
                "—";

            fila.innerHTML = `

                <td>
                    ${escaparHTML(fecha)}
                </td>

                <td>
                    ${escaparHTML(hora)}
                </td>

                <td>
                    ${escaparHTML(
                        formatearValor(
                            medicion.mq5,
                            " ppm"
                        )
                    )}
                </td>

                <td>
                    ${escaparHTML(
                        formatearValor(
                            medicion.dht11_temperatura,
                            " °C"
                        )
                    )}
                </td>

                <td>
                    ${escaparHTML(
                        formatearValor(
                            medicion.dht11_humedad,
                            " %"
                        )
                    )}
                </td>

                <td>
                    ${escaparHTML(
                        formatearValor(
                            medicion.ds18b20,
                            " °C"
                        )
                    )}
                </td>

            `;

            tabla.appendChild(
                fila
            );
        }
    );
}


// =====================================================
// ACTUALIZAR SENSORES ACTUALES
// =====================================================

function actualizarValoresActuales(
    medicion
) {

    const mq5 =
        document.getElementById(
            "actualMQ5"
        );

    const dhtTemp =
        document.getElementById(
            "actualDHTTemp"
        );

    const dhtHum =
        document.getElementById(
            "actualDHTHum"
        );

    const ds18 =
        document.getElementById(
            "actualDS18"
        );


    if (!medicion) {

        if (mq5) {
            mq5.textContent = "—";
        }

        if (dhtTemp) {
            dhtTemp.textContent = "—";
        }

        if (dhtHum) {
            dhtHum.textContent = "—";
        }

        if (ds18) {
            ds18.textContent = "—";
        }

        return;
    }


    if (mq5) {

        mq5.textContent =
            formatearValor(
                medicion.mq5,
                " ppm"
            );
    }


    if (dhtTemp) {

        dhtTemp.textContent =
            formatearValor(
                medicion.dht11_temperatura,
                " °C"
            );
    }


    if (dhtHum) {

        dhtHum.textContent =
            formatearValor(
                medicion.dht11_humedad,
                " %"
            );
    }


    if (ds18) {

        ds18.textContent =
            formatearValor(
                medicion.ds18b20,
                " °C"
            );
    }


    // =================================================
    // ESTADOS
    // =================================================

    const estadoMQ5 =
        document.getElementById(
            "estadoMQ5"
        );

    const estadoDHT =
        document.getElementById(
            "estadoDHT11"
        );

    const estadoDS =
        document.getElementById(
            "estadoDS18"
        );


    if (estadoMQ5) {

        estadoMQ5.textContent =
            medicion.mq5 !== null
                ? "Registrado"
                : "Esperando";
    }


    if (estadoDHT) {

        estadoDHT.textContent =
            (
                medicion.dht11_temperatura !== null ||
                medicion.dht11_humedad !== null
            )
                ? "Registrado"
                : "Esperando";
    }


    if (estadoDS) {

        estadoDS.textContent =
            medicion.ds18b20 !== null
                ? "Registrado"
                : "Esperando";
    }
}


// =====================================================
// ACTUALIZACIÓN AUTOMÁTICA
// =====================================================

function iniciarActualizacion() {

    setInterval(
        cargarMediciones,
        10000
    );
}


// =====================================================
// INICIO
// =====================================================

document.addEventListener(
    "DOMContentLoaded",
    function () {

        console.log(
            "================================="
        );

        console.log(
            "AULAVIVA - PROYECTO.JS"
        );

        console.log(
            "================================="
        );

        cargarProyecto();

        iniciarActualizacion();

    }
);
