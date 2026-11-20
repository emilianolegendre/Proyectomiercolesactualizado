<?php

session_start();

header("Content-Type: application/json; charset=utf-8");

require_once "config/database.php";


// =====================================================
// RESPUESTA JSON
// =====================================================

function responder($datos)
{
    echo json_encode(
        $datos,
        JSON_UNESCAPED_UNICODE
    );

    exit;
}


// =====================================================
// SESIÓN
// =====================================================

if (!isset($_SESSION["usuario"])) {

    responder([
        "ok" => false,
        "mensaje" => "No hay una sesión iniciada."
    ]);

}


$accion = $_GET["accion"] ?? "";

$usuario = $_SESSION["usuario"];

$rol = $_SESSION["rol"] ?? "estudiante";


// =====================================================
// FUNCIONES DE PERMISOS
// =====================================================

function puedeCrearProyecto($rol)
{
    return (
        $rol === "docente" ||
        $rol === "administrador"
    );
}


function puedeEditarProyecto($rol)
{
    return $rol === "administrador";
}


function puedeEliminarProyecto($rol)
{
    return $rol === "administrador";
}


// =====================================================
// CREAR / OBTENER TIPO DE SENSOR
// =====================================================

function obtenerTipoSensor(
    PDO $conexion,
    $nombre,
    $descripcion
) {

    $sql = "
        SELECT id_ts
        FROM tipo_sensor
        WHERE nombre = ?
        LIMIT 1
    ";

    $stmt = $conexion->prepare($sql);

    $stmt->execute([
        $nombre
    ]);

    $id = $stmt->fetchColumn();

    if ($id) {

        return (int)$id;

    }


    $sql = "
        INSERT INTO tipo_sensor
        (
            nombre,
            Descripcion,
            k_conversion
        )
        VALUES
        (
            ?,
            ?,
            1
        )
    ";

    $stmt = $conexion->prepare($sql);

    $stmt->execute([
        $nombre,
        $descripcion
    ]);

    return (int)$conexion->lastInsertId();
}


// =====================================================
// CREAR PROYECTO
// =====================================================

if ($accion === "crear") {

    if (!puedeCrearProyecto($rol)) {

        responder([
            "ok" => false,
            "mensaje" =>
                "No tenés permiso para crear proyectos."
        ]);

    }


    try {

        $datos = json_decode(
            file_get_contents("php://input"),
            true
        );


        if (!is_array($datos)) {

            responder([
                "ok" => false,
                "mensaje" =>
                    "Los datos recibidos no son válidos."
            ]);

        }


        // -------------------------------------------------
        // DATOS DEL FORMULARIO
        // -------------------------------------------------

        $titulo = trim(
            $datos["titulo"] ?? ""
        );

        $descripcion = trim(
            $datos["descripcion"] ?? ""
        );

        $responsable = trim(
            $datos["responsable"] ?? ""
        );


        // -------------------------------------------------
        // VALIDAR TÍTULO
        // -------------------------------------------------

        if ($titulo === "") {

            responder([
                "ok" => false,
                "mensaje" =>
                    "Ingresá un título para el proyecto."
            ]);

        }


        // -------------------------------------------------
        // RESPONSABLE
        // -------------------------------------------------

        if ($responsable === "") {

            $nombre =
                $_SESSION["nombre"] ?? "";

            $apellido =
                $_SESSION["apellido"] ?? "";

            $responsable = trim(
                $nombre . " " . $apellido
            );

        }


        if ($responsable === "") {

            $responsable = $usuario;

        }


        // -------------------------------------------------
        // TRANSACCIÓN
        // -------------------------------------------------

        $conexion->beginTransaction();


        // -------------------------------------------------
        // INSERTAR PROYECTO
        //
        // IMPORTANTE:
        // No usamos curso ni Materia porque el formulario
        // actual no los tiene.
        // -------------------------------------------------

        $sql = "

            INSERT INTO proyectos
            (
                Usuario,
                Descripcion,
                Titulo,
                Responsable
            )

            VALUES
            (
                ?,
                ?,
                ?,
                ?
            )

        ";


        $stmt =
            $conexion->prepare($sql);


        $stmt->execute([

            $usuario,

            $descripcion,

            $titulo,

            $responsable

        ]);


        $idProyecto =
            (int)$conexion->lastInsertId();


        // -------------------------------------------------
        // CREAR MÓDULO
        // -------------------------------------------------

        $sqlModulo = "

            INSERT INTO conf_modulos
            (
                Id_proyecto,
                Data_mediciones,
                Data_guardado,
                Nombre,
                Descripcion
            )

            VALUES
            (
                ?,
                ?,
                ?,
                ?,
                ?
            )

        ";


        $stmtModulo =
            $conexion->prepare($sqlModulo);


        $stmtModulo->execute([

            $idProyecto,

            4,

            3600,

            "Monitoreo de sensores",

            "Módulo principal para el registro de sensores del proyecto."

        ]);


        $idModulo =
            (int)$conexion->lastInsertId();


        // -------------------------------------------------
        // CREAR TIPOS DE SENSOR
        // -------------------------------------------------

        $idMQ5 = obtenerTipoSensor(

            $conexion,

            "MQ-5",

            "Sensor para detección de gases combustibles."

        );


        $idDHT11 = obtenerTipoSensor(

            $conexion,

            "DHT11",

            "Sensor de temperatura y humedad."

        );


        $idDS18B20 = obtenerTipoSensor(

            $conexion,

            "DS18B20",

            "Sensor digital de temperatura de precisión."

        );


        // -------------------------------------------------
        // ASOCIAR SENSORES AL PROYECTO
        // -------------------------------------------------

        $sqlSensor = "

            INSERT INTO sensores_proyecto
            (
                id_proyecto,
                id_ts,
                t_registro,
                t_muestra,
                descripcion
            )

            VALUES
            (
                ?,
                ?,
                ?,
                ?,
                ?
            )

        ";


        $stmtSensor =
            $conexion->prepare($sqlSensor);


        // MQ-5

        $stmtSensor->execute([

            $idProyecto,

            $idMQ5,

            "01:00:00",

            "00:00:30",

            "Sensor MQ-5 - detección de gases combustibles."

        ]);


        // DHT11 temperatura

        $stmtSensor->execute([

            $idProyecto,

            $idDHT11,

            "01:00:00",

            "00:00:30",

            "Sensor DHT11 - temperatura."

        ]);


        // DHT11 humedad

        $stmtSensor->execute([

            $idProyecto,

            $idDHT11,

            "01:00:00",

            "00:00:30",

            "Sensor DHT11 - humedad."

        ]);


        // DS18B20

        $stmtSensor->execute([

            $idProyecto,

            $idDS18B20,

            "01:00:00",

            "00:00:30",

            "Sensor DS18B20 - temperatura de precisión."

        ]);


        // -------------------------------------------------
        // CONFIRMAR
        // -------------------------------------------------

        $conexion->commit();


        responder([

            "ok" => true,

            "mensaje" =>
                "Proyecto creado correctamente.",

            "id" =>
                $idProyecto

        ]);


    } catch (PDOException $e) {

        if ($conexion->inTransaction()) {

            $conexion->rollBack();

        }


        responder([

            "ok" => false,

            "mensaje" =>
                "Error MySQL al crear el proyecto: " .
                $e->getMessage()

        ]);

    }

}


// =====================================================
// LISTAR PROYECTOS
// =====================================================

if ($accion === "listar") {

    try {

        $sql = "

            SELECT

                Id_proyecto AS id,

                Usuario AS usuario,

                Descripcion AS descripcion,

                Titulo AS titulo,

                Responsable AS responsable

            FROM proyectos

            ORDER BY Id_proyecto DESC

        ";


        $stmt =
            $conexion->query($sql);


        $proyectos =
            $stmt->fetchAll();


        responder([

            "ok" => true,

            "proyectos" =>
                $proyectos

        ]);


    } catch (PDOException $e) {

        responder([

            "ok" => false,

            "mensaje" =>
                "Error al listar proyectos: " .
                $e->getMessage()

        ]);

    }

}


// =====================================================
// OBTENER PROYECTO
// =====================================================

if ($accion === "obtener") {

    $id = intval(
        $_GET["id"] ?? 0
    );


    if ($id <= 0) {

        responder([

            "ok" => false,

            "mensaje" =>
                "ID inválido."

        ]);

    }


    try {

        $sql = "

            SELECT

                Id_proyecto AS id,

                Usuario AS usuario,

                Descripcion AS descripcion,

                Titulo AS titulo,

                Responsable AS responsable

            FROM proyectos

            WHERE Id_proyecto = ?

            LIMIT 1

        ";


        $stmt =
            $conexion->prepare($sql);


        $stmt->execute([
            $id
        ]);


        $proyecto =
            $stmt->fetch();


        if (!$proyecto) {

            responder([

                "ok" => false,

                "mensaje" =>
                    "No se encontró el proyecto."

            ]);

        }


        responder([

            "ok" => true,

            "proyecto" =>
                $proyecto

        ]);


    } catch (PDOException $e) {

        responder([

            "ok" => false,

            "mensaje" =>
                "Error al obtener el proyecto: " .
                $e->getMessage()

        ]);

    }

}


// =====================================================
// MEDICIONES
// =====================================================

if ($accion === "mediciones") {

    $idProyecto = intval(
        $_GET["id_proyecto"] ?? 0
    );


    if ($idProyecto <= 0) {

        responder([

            "ok" => false,

            "mensaje" =>
                "ID de proyecto inválido."

        ]);

    }


    try {

        $sql = "

            SELECT

                m.Id_medicion AS id_medicion,

                m.Id_proyecto AS id_proyecto,

                m.Id_modulo AS id_modulo,

                m.id_sp AS id_sp,

                m.Valor AS valor,

                m.Tiempo AS fecha_hora,

                sp.descripcion AS sensor_descripcion,

                ts.nombre AS tipo_sensor

            FROM mediciones m

            INNER JOIN sensores_proyecto sp
                ON sp.id_sp = m.id_sp

            INNER JOIN tipo_sensor ts
                ON ts.id_ts = sp.id_ts

            WHERE m.Id_proyecto = ?

            ORDER BY m.Tiempo DESC

        ";


        $stmt =
            $conexion->prepare($sql);


        $stmt->execute([
            $idProyecto
        ]);


        $mediciones =
            $stmt->fetchAll();


        responder([

            "ok" => true,

            "mediciones" =>
                $mediciones

        ]);


    } catch (PDOException $e) {

        responder([

            "ok" => false,

            "mensaje" =>
                "Error al consultar mediciones: " .
                $e->getMessage()

        ]);

    }

}


// =====================================================
// GUARDAR MEDICIÓN
// =====================================================

if ($accion === "guardar_medicion") {

    try {

        $datos = json_decode(

            file_get_contents(
                "php://input"
            ),

            true

        );


        if (!is_array($datos)) {

            responder([

                "ok" => false,

                "mensaje" =>
                    "Datos inválidos."

            ]);

        }


        $idProyecto = intval(

            $datos["id_proyecto"] ?? 0

        );


        if ($idProyecto <= 0) {

            responder([

                "ok" => false,

                "mensaje" =>
                    "ID de proyecto inválido."

            ]);

        }


        // -------------------------------------------------
        // VALORES DE SENSORES
        // -------------------------------------------------

        $mq5 =
            $datos["mq5"] ?? null;

        $dhtTemperatura =
            $datos["dht11_temperatura"] ?? null;

        $dhtHumedad =
            $datos["dht11_humedad"] ?? null;

        $ds18b20 =
            $datos["ds18b20"] ?? null;


        $hayDatos = (

            $mq5 !== null ||

            $dhtTemperatura !== null ||

            $dhtHumedad !== null ||

            $ds18b20 !== null

        );


        if (!$hayDatos) {

            responder([

                "ok" => true,

                "guardado" => false,

                "mensaje" =>
                    "No llegaron datos de sensores."

            ]);

        }


        // -------------------------------------------------
        // VERIFICAR PROYECTO
        // -------------------------------------------------

        $sql = "

            SELECT Id_proyecto

            FROM proyectos

            WHERE Id_proyecto = ?

            LIMIT 1

        ";


        $stmt =
            $conexion->prepare($sql);


        $stmt->execute([
            $idProyecto
        ]);


        if (!$stmt->fetch()) {

            responder([

                "ok" => false,

                "mensaje" =>
                    "El proyecto no existe."

            ]);

        }


        // -------------------------------------------------
        // OBTENER MÓDULO
        // -------------------------------------------------

        $sql = "

            SELECT Id_modulo

            FROM conf_modulos

            WHERE Id_proyecto = ?

            ORDER BY Id_modulo ASC

            LIMIT 1

        ";


        $stmt =
            $conexion->prepare($sql);


        $stmt->execute([
            $idProyecto
        ]);


        $idModulo =
            $stmt->fetchColumn();


        if (!$idModulo) {

            responder([

                "ok" => false,

                "mensaje" =>
                    "El proyecto no tiene un módulo configurado."

            ]);

        }


        // -------------------------------------------------
        // OBTENER SENSORES
        // -------------------------------------------------

        $sql = "

            SELECT

                id_sp,

                descripcion

            FROM sensores_proyecto

            WHERE id_proyecto = ?

            ORDER BY id_sp ASC

        ";


        $stmt =
            $conexion->prepare($sql);


        $stmt->execute([
            $idProyecto
        ]);


        $sensores =
            $stmt->fetchAll();


        $idMQ5 = null;
        $idDHTTemp = null;
        $idDHTHum = null;
        $idDS18 = null;


        foreach ($sensores as $sensor) {

            $descripcion =
                strtolower(
                    $sensor["descripcion"]
                );


            if (
                strpos(
                    $descripcion,
                    "mq-5"
                ) !== false
            ) {

                $idMQ5 =
                    $sensor["id_sp"];

            }


            if (
                strpos(
                    $descripcion,
                    "dht11"
                ) !== false &&
                strpos(
                    $descripcion,
                    "temperatura"
                ) !== false
            ) {

                $idDHTTemp =
                    $sensor["id_sp"];

            }


            if (
                strpos(
                    $descripcion,
                    "dht11"
                ) !== false &&
                strpos(
                    $descripcion,
                    "humedad"
                ) !== false
            ) {

                $idDHTHum =
                    $sensor["id_sp"];

            }


            if (
                strpos(
                    $descripcion,
                    "ds18b20"
                ) !== false
            ) {

                $idDS18 =
                    $sensor["id_sp"];

            }

        }


        // -------------------------------------------------
        // INSERTAR MEDICIÓN
        // -------------------------------------------------

        $insertar = function (
            $idSensor,
            $valor
        ) use (
            $conexion,
            $idProyecto,
            $idModulo
        ) {

            if (
                $idSensor === null ||
                $valor === null ||
                $valor === ""
            ) {

                return false;

            }


            // ---------------------------------------------
            // UNA MEDICIÓN POR SENSOR POR HORA
            // ---------------------------------------------

            $sqlExiste = "

                SELECT Id_medicion

                FROM mediciones

                WHERE Id_proyecto = ?

                AND Id_modulo = ?

                AND id_sp = ?

                AND Tiempo >= DATE_FORMAT(
                    NOW(),
                    '%Y-%m-%d %H:00:00'
                )

                AND Tiempo < DATE_ADD(
                    DATE_FORMAT(
                        NOW(),
                        '%Y-%m-%d %H:00:00'
                    ),
                    INTERVAL 1 HOUR
                )

                LIMIT 1

            ";


            $stmtExiste =
                $conexion->prepare(
                    $sqlExiste
                );


            $stmtExiste->execute([

                $idProyecto,

                $idModulo,

                $idSensor

            ]);


            if ($stmtExiste->fetch()) {

                return false;

            }


            // ---------------------------------------------
            // INSERTAR
            // ---------------------------------------------

            $sqlInsertar = "

                INSERT INTO mediciones
                (
                    Id_proyecto,
                    Id_modulo,
                    Valor,
                    Tiempo,
                    id_sp
                )

                VALUES
                (
                    ?,
                    ?,
                    ?,
                    NOW(),
                    ?
                )

            ";


            $stmtInsertar =
                $conexion->prepare(
                    $sqlInsertar
                );


            $stmtInsertar->execute([

                $idProyecto,

                $idModulo,

                $valor,

                $idSensor

            ]);


            return true;

        };


        $guardadas = 0;


        if (
            $insertar(
                $idMQ5,
                $mq5
            )
        ) {

            $guardadas++;

        }


        if (
            $insertar(
                $idDHTTemp,
                $dhtTemperatura
            )
        ) {

            $guardadas++;

        }


        if (
            $insertar(
                $idDHTHum,
                $dhtHumedad
            )
        ) {

            $guardadas++;

        }


        if (
            $insertar(
                $idDS18,
                $ds18b20
            )
        ) {

            $guardadas++;

        }


        responder([

            "ok" => true,

            "guardado" =>
                $guardadas > 0,

            "cantidad" =>
                $guardadas,

            "mensaje" =>
                $guardadas > 0
                    ? "Mediciones guardadas correctamente."
                    : "Ya existen mediciones para esta hora o no llegaron datos."

        ]);


    } catch (PDOException $e) {

        responder([

            "ok" => false,

            "mensaje" =>
                "Error al guardar medición: " .
                $e->getMessage()

        ]);

    }

}


// =====================================================
// EDITAR PROYECTO
// SOLO ADMINISTRADOR
// =====================================================

if ($accion === "editar") {

    if (!puedeEditarProyecto($rol)) {

        responder([

            "ok" => false,

            "mensaje" =>
                "Solo un administrador puede editar proyectos."

        ]);

    }


    try {

        $datos = json_decode(
            file_get_contents("php://input"),
            true
        );


        if (!is_array($datos)) {

            responder([

                "ok" => false,

                "mensaje" =>
                    "Datos inválidos."

            ]);

        }


        $id = intval(
            $datos["id"] ?? 0
        );


        $titulo = trim(
            $datos["titulo"] ?? ""
        );


        $descripcion = trim(
            $datos["descripcion"] ?? ""
        );


        $responsable = trim(
            $datos["responsable"] ?? ""
        );


        if (
            $id <= 0 ||
            $titulo === ""
        ) {

            responder([

                "ok" => false,

                "mensaje" =>
                    "Faltan datos obligatorios."

            ]);

        }


        // -------------------------------------------------
        // RESPONSABLE
        // -------------------------------------------------

        if ($responsable === "") {

            $nombre =
                $_SESSION["nombre"] ?? "";

            $apellido =
                $_SESSION["apellido"] ?? "";

            $responsable = trim(
                $nombre . " " . $apellido
            );

        }


        if ($responsable === "") {

            $responsable = $usuario;

        }


        // -------------------------------------------------
        // ACTUALIZAR
        // -------------------------------------------------

        $sql = "

            UPDATE proyectos

            SET

                Titulo = ?,

                Descripcion = ?,

                Responsable = ?

            WHERE Id_proyecto = ?

        ";


        $stmt =
            $conexion->prepare($sql);


        $stmt->execute([

            $titulo,

            $descripcion,

            $responsable,

            $id

        ]);


        responder([

            "ok" => true,

            "mensaje" =>
                "Proyecto actualizado correctamente."

        ]);


    } catch (PDOException $e) {

        responder([

            "ok" => false,

            "mensaje" =>
                "Error al editar proyecto: " .
                $e->getMessage()

        ]);

    }

}


// =====================================================
// ELIMINAR PROYECTO
// SOLO ADMINISTRADOR
// =====================================================

if ($accion === "eliminar") {

    if (!puedeEliminarProyecto($rol)) {

        responder([

            "ok" => false,

            "mensaje" =>
                "Solo un administrador puede eliminar proyectos."

        ]);

    }


    $id = intval(
        $_GET["id"] ?? 0
    );


    if ($id <= 0) {

        responder([

            "ok" => false,

            "mensaje" =>
                "ID inválido."

        ]);

    }


    try {

        $sql = "

            DELETE FROM proyectos

            WHERE Id_proyecto = ?

        ";


        $stmt =
            $conexion->prepare($sql);


        $stmt->execute([
            $id
        ]);


        responder([

            "ok" => true,

            "mensaje" =>
                "Proyecto eliminado correctamente."

        ]);


    } catch (PDOException $e) {

        responder([

            "ok" => false,

            "mensaje" =>
                "Error al eliminar proyecto: " .
                $e->getMessage()

        ]);

    }

}


// =====================================================
// ERROR
// =====================================================

responder([

    "ok" => false,

    "mensaje" =>
        "Acción no válida: " . $accion

]);

?>
