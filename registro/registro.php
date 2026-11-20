<?php

session_start();


// =====================================================
// CONEXIÓN
// =====================================================

require_once __DIR__ . "/../config/database.php";


// =====================================================
// SOLO POST
// =====================================================

if (
    $_SERVER["REQUEST_METHOD"] !== "POST"
) {

    header(
        "Location: registro.html"
    );

    exit;

}


// =====================================================
// DATOS
// =====================================================

$id_usuario =
    trim(
        $_POST["id_usuario"] ?? ""
    );


$nombre =
    trim(
        $_POST["nombre"] ?? ""
    );


$apellido =
    trim(
        $_POST["apellido"] ?? ""
    );


$password =
    $_POST["password"] ?? "";


$password_confirm =
    $_POST["password_confirm"] ?? "";


$institucion =
    trim(
        $_POST["institucion"] ?? ""
    );


// =====================================================
// VALIDAR CAMPOS
// =====================================================

if (
    $id_usuario === "" ||
    $nombre === "" ||
    $apellido === "" ||
    $password === "" ||
    $password_confirm === ""
) {

    die(
        "Completá todos los campos obligatorios."
    );

}


// =====================================================
// VALIDAR CONTRASEÑAS
// =====================================================

if (
    $password !==
    $password_confirm
) {

    die(
        "Las contraseñas no coinciden."
    );

}


// =====================================================
// LONGITUD
// =====================================================

if (
    strlen($password) < 6
) {

    die(
        "La contraseña debe tener al menos 6 caracteres."
    );

}


// =====================================================
// VALIDAR USUARIO
// =====================================================

// Permitimos letras, números, guión y guión bajo.

if (
    !preg_match(
        '/^[a-zA-Z0-9_-]+$/',
        $id_usuario
    )
) {

    die(
        "El usuario solo puede contener letras, números, guión y guión bajo."
    );

}


// =====================================================
// COMPROBAR SI YA EXISTE
// =====================================================

$sql = "
    SELECT id_usuario
    FROM usuarios
    WHERE id_usuario = ?
    LIMIT 1
";


$stmt =
    $conexion->prepare($sql);


$stmt->execute([
    $id_usuario
]);


if ($stmt->fetch()) {

    die(
        "Ese nombre de usuario ya está registrado."
    );

}


// =====================================================
// ROL
// =====================================================
//
// IMPORTANTE:
// El usuario NO puede elegir el rol.
// Todos los registros nuevos son estudiantes.
// El administrador puede cambiarlo después.
// =====================================================

$rol = "estudiante";


// =====================================================
// HASH DE CONTRASEÑA
// =====================================================

$passwordHash =
    password_hash(
        $password,
        PASSWORD_DEFAULT
    );


// =====================================================
// INSERTAR
// =====================================================

$sql = "
    INSERT INTO usuarios
    (
        id_usuario,
        Nombre,
        Apellido,
        Rol,
        Password,
        Institucion
    )
    VALUES
    (
        ?,
        ?,
        ?,
        ?,
        ?,
        ?
    )
";


$stmt =
    $conexion->prepare($sql);


try {

    $stmt->execute([

        $id_usuario,

        $nombre,

        $apellido,

        $rol,

        $passwordHash,

        $institucion

    ]);

} catch (PDOException $e) {

    die(
        "No se pudo crear la cuenta. "
        . "Verificá que la tabla usuarios tenga los campos correctos."
    );

}


// =====================================================
// VOLVER AL LOGIN
// =====================================================

header(
    "Location: ../login/login.html?registro=ok"
);

exit;

?>
