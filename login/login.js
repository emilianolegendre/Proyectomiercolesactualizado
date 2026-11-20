document.addEventListener("DOMContentLoaded", () => {

    const params =
        new URLSearchParams(
            window.location.search
        );


    if (params.get("registro") === "ok") {

        alert(
            "Cuenta creada correctamente. Ahora podés iniciar sesión."
        );

    }


    if (params.get("error") === "1") {

        alert(
            "Usuario o contraseña incorrectos."
        );

    }

});
