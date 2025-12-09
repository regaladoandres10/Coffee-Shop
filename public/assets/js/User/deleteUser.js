document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".btn-delete-user").forEach(btn => {
        btn.addEventListener("click", async function () {

            const userId = this.dataset.userId;
            const userName = this.dataset.userName;

            if (!confirm(`¿Seguro que deseas eliminar al usuario "${userName}"? Esta acción no se puede deshacer.`)) {
                return;
            }

            try {
                const response = await fetch(`http://localhost:3000/public/api.php?resource=users&id=${userId}`, {
                    method: "DELETE"
                });

                const result = await response.json();
                console.log("Resultado DELETE:", result);

                if (result.success) {
                    alert("Usuario eliminado correctamente");
                    location.reload(); // recargar la lista
                } else {
                    alert("Error al eliminar: " + result.message);
                }

            } catch (error) {
                console.error("Error al eliminar usuario:", error);
                alert("Hubo un error al tratar de eliminar el usuario.");
            }
        });
    });
});
