document.addEventListener(
    "DOMContentLoaded",
    () => {


        const formulario =
            document.querySelector("form");


        const password =
            document.getElementById(
                "password"
            );


        const passwordConfirm =
            document.getElementById(
                "password_confirm"
            );


        formulario.addEventListener(
            "submit",
            (event) => {


                // ---------------------------------
                // CONTRASEÑAS DIFERENTES
                // ---------------------------------

                if (
                    password.value !==
                    passwordConfirm.value
                ) {

                    event.preventDefault();

                    alert(
                        "Las contraseñas no coinciden."
                    );

                    passwordConfirm.focus();

                    return;

                }


                // ---------------------------------
                // MÍNIMO 6 CARACTERES
                // ---------------------------------

                if (
                    password.value.length < 6
                ) {

                    event.preventDefault();

                    alert(
                        "La contraseña debe tener al menos 6 caracteres."
                    );

                    password.focus();

                    return;

                }

            }
        );

    }
);
