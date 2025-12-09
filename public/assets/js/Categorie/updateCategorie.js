document.addEventListener("DOMContentLoaded", function () {

    document.querySelectorAll(".edit-category-btn").forEach(btn => {
        btn.addEventListener("click", async function () {

            const categoryId = this.dataset.id;

            try {
                const response = await fetch(`http://localhost:3000/public/api.php?resource=categories&id=${categoryId}`);
                const result = await response.json();

                if (!result.success) {
                    console.error("API error:", result.message);
                    return;
                }

                const category = result.data;

                //Llenar modal
                document.getElementById("editCategoryId").value = category.idCategorie;
                document.getElementById("editCategoryName").value = category.name;
                document.getElementById("editCategoryDescription").value = category.description ?? "";
                document.getElementById("editCategoryIsActive").value = category.isActive;

                //Abrir modal
                const modal = new bootstrap.Modal(document.getElementById("editCategoryModal"));
                modal.show();

            } catch (error) {
                console.error("Error al cargar la categoría:", error);
            }

        });
    });

});

//Enviar formulario de actualización
document.addEventListener("DOMContentLoaded", () => {

    const editForm = document.getElementById("editCategoryForm");

    if (!editForm) {
        console.error("No se encontró el formulario de edición de categoría");
        return;
    }

    editForm.addEventListener("submit", async function (e) {
        e.preventDefault();

        console.log("Enviando actualización de categoría...");

        const categoryId = document.getElementById("editCategoryId").value;

        const formData = {
            id: categoryId,
            name: document.getElementById("editCategoryName").value,
            description: document.getElementById("editCategoryDescription").value,
            isActive: document.getElementById("editCategoryIsActive").value
        };

        try {

            const response = await fetch(`http://localhost:3000/public/api.php?resource=categories&id=${categoryId}`, {
                method: "PUT",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(formData)
            });

            const result = await response.json();
            console.log("Respuesta del servidor:", result);

            if (result.success) {
                alert("Categoría actualizada correctamente");
                location.reload();
            } else {
                alert("Error al actualizar: " + result.message);
            }

        } catch (error) {
            console.error("Error al actualizar categoría:", error);
        }

    });

});

function editCategoryFromView(btn) {
    const categoryId = btn.dataset.id;

    //Cargar datos
    loadCategoryData(categoryId);

    //Abrir modal
    const modal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
    modal.show();
}
