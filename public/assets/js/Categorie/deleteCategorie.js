document.addEventListener("DOMContentLoaded", () => {

    document.querySelectorAll(".delete-category-btn").forEach(btn => {
        btn.addEventListener("click", async function () {

            const categoryId = this.dataset.id;
            const categoryName = this.dataset.name;

            if (!confirm(`¿Seguro que deseas eliminar la categoría "${categoryName}"? Esta acción no se puede deshacer.`)) {
                return;
            }

            try {
                const response = await fetch(`http://localhost:3000/public/api.php?resource=categories&id=${categoryId}`, {
                    method: "DELETE"
                });

                const result = await response.json();
                console.log("Resultado DELETE:", result);

                if (result.success) {
                    alert("Categoría eliminada correctamente");
                    location.reload(); //recargar la página o tabla
                } else {
                    alert("Error al eliminar: " + result.message);
                }

            } catch (error) {
                console.error("Error al eliminar categoría:", error);
                alert("Hubo un error al intentar eliminar la categoría.");
            }
        });
    });

});
