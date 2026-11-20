<?php

session_start();


// =====================================================
// CONEXIÓN A LA BASE DE DATOS
// =====================================================

require_once __DIR__ . "/../config/database.php";


// =====================================================
// SOLO POST
// =====================================================

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: login.html");

    exit;

}


// =====================================================
// DATOS DEL FORMULARIO
// =====================================================

$id_usuario = trim(
    $_POST["id_usuario"] ?? ""
);

$password = $_POST["password"] ?? "";


// =====================================================
// VALIDAR
// =====================================================

if (
    $id_usuario === "" ||
    $password === ""
) {

    die(
        "Completá usuario y contraseña."
    );

}


// =====================================================
// BUSCAR USUARIO
// =====================================================

$sql = "
    SELECT
        id_usuario,
        Nombre,
        Apellido,
        Rol,
        Password,
        Institucion
    FROM usuarios
    WHERE id_usuario = ?
    LIMIT 1
";


$stmt = $conexion->prepare($sql);

$stmt->execute([
    $id_usuario
]);


$usuario =
    $stmt->fetch(PDO::FETCH_ASSOC);


// =====================================================
// USUARIO NO EXISTE
// =====================================================

if (!$usuario) {

    die(
        "Usuario o contraseña incorrectos."
    );

}


// =====================================================
// VERIFICAR CONTRASEÑA
// =====================================================

$passwordCorrecta = false;


// -----------------------------------------------------
// CASO 1:
// contraseña guardada con password_hash()
// -----------------------------------------------------

if (
    password_verify(
        $password,
        $usuario["Password"]
    )
) {

    $passwordCorrecta = true;

}


// -----------------------------------------------------
// CASO 2:
// usuario viejo con contraseña en texto normal
// -----------------------------------------------------

elseif (
    hash_equals(
        (string) $usuario["Password"],
        (string) $password
    )
) {

    $passwordCorrecta = true;


    // ---------------------------------------------
    // ACTUALIZAR AUTOMÁTICAMENTE A HASH
    // ---------------------------------------------

    $nuevoHash =
        password_hash(
            $password,
            PASSWORD_DEFAULT
        );


    $sqlActualizar = "
        UPDATE usuarios
        SET Password = ?
        WHERE id_usuario = ?
    ";


    $stmtActualizar =
        $conexion->prepare(
            $sqlActualizar
        );


    $stmtActualizar->execute([
        $nuevoHash,
        $usuario["id_usuario"]
    ]);

}


// =====================================================
// CONTRASEÑA INCORRECTA
// =====================================================

if (!$passwordCorrecta) {

    die(
        "Usuario o contraseña incorrectos."
    );

}


// =====================================================
// SESIÓN
// =====================================================

$_SESSION["usuario"] =
    $usuario["id_usuario"];


$_SESSION["nombre"] =
    $usuario["Nombre"];


$_SESSION["apellido"] =
    $usuario["Apellido"];


$_SESSION["rol"] =
    $usuario["Rol"];


$_SESSION["institucion"] =
    $usuario["Institucion"] ?? "";


// =====================================================
// REDIRECCIÓN
// =====================================================

header(
    "Location: ../index.php"
);

exit;

?>
