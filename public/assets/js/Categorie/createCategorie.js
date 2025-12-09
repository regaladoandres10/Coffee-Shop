//Lógica para crear categorías
const createCategoryForm = document.getElementById('createCategoryForm');
const categoryFormMessage = document.getElementById('categoryFormMessage');


if (!categoryFormMessage) {
    const msgDiv = document.createElement("div");
    msgDiv.id = "categoryFormMessage";
    msgDiv.className = "alert d-none";
    document.querySelector("#createCategoryForm .modal-body").prepend(msgDiv);
}

const messageBox = document.getElementById("categoryFormMessage");

if (createCategoryForm) {
    createCategoryForm.addEventListener('submit', async function (event) {
        event.preventDefault();

        const formData = new FormData(createCategoryForm);

        //Convertir FormData a JSON para enviarlo como application/json
        const data = {};
        formData.forEach((value, key) => data[key] = value);

        //Enviar datos a la API
        const response = await fetch("http://localhost:3000/public/api.php?resource=categories", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        //Mostrar mensaje
        messageBox.classList.remove("d-none");

        if (result.success) {
            messageBox.className = "alert alert-success";
            messageBox.textContent = result.message || "Categoría creada correctamente.";

            //Limpiar formulario
            createCategoryForm.reset();

            //Cerrar modal después de 1 segundo
            setTimeout(() => {
                const modal = bootstrap.Modal.getInstance(
                    document.getElementById('createCategoryModal')
                );
                if (modal) modal.hide();

                messageBox.classList.add("d-none");
            }, 800);

        } else {
            messageBox.className = "alert alert-danger";
            messageBox.textContent = result.message || "Error al crear categoría.";
        }
    });
}
