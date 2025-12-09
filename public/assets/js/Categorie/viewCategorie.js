document.addEventListener("DOMContentLoaded", () => {
    const viewButtons = document.querySelectorAll(".view-category-btn");

    viewButtons.forEach(btn => {
        btn.addEventListener("click", async function () {
            const categoryId = this.dataset.id;

            await loadCategoryDetails(categoryId); //Carga información al modal
        });
    });
});

//Función que obtiene los datos desde tu API REAL
async function loadCategoryDetails(categoryId) {
    const url = `${API_URL}?resource=categories&id=${categoryId}`;

    try {
        const response = await fetch(url);
        const result = await response.json();

        if (!result.success) {
            console.error("Error cargando categoría:", result.message);
            return;
        }

        const cat = result.data;

        //Llenar cmapos del modal
        document.getElementById("viewCategoryName").textContent = cat.name ?? "-";
        document.getElementById("viewCategoryDescription").textContent = cat.description ?? "-";

        //Estado
        document.getElementById("viewCategoryState").textContent =
            cat.isActive == 1 ? "Activo" : "Inactivo";

        //Imagen
        const img = document.getElementById("viewCategoryImage");

        if (cat.image && cat.image !== "") {
            img.src = `/uploads/categories/${cat.image}`;
            img.style.display = "block";
        } else {
            img.style.display = "none";
        }

    } catch (error) {
        console.error("Error:", error);
    }
}
