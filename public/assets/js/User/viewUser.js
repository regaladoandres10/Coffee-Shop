document.addEventListener("DOMContentLoaded", () => {
    const viewButtons = document.querySelectorAll(".view-user-btn");

    viewButtons.forEach(btn => {
        btn.addEventListener("click", async function () {
            const userId = this.dataset.id;

            await loadUserDetails(userId); //Carga información al modal
        });
    });
});

//Función que obtiene los datos desde tu API REAL
async function loadUserDetails(userId) {
    const url = `${API_URL}?resource=users&id=${userId}`;

    try {
        const response = await fetch(url);
        const result = await response.json();

        if (!result.success) {
            console.error("Error cargando usuario:", result.message);
            return;
        }

        const user = result.data;

        // --- LLENAR CAMPOS DEL MODAL ---
        document.getElementById("viewUserAvatar").textContent = user.name.charAt(0).toUpperCase();
        document.getElementById("viewUserName").textContent = user.name;
        document.getElementById("viewUsername").textContent = user.nameUser;
        document.getElementById("viewUserEmail").textContent = user.RoleName; 
        document.getElementById("viewUserPhone").textContent = user.phone;
        document.getElementById("viewUserStatus").textContent = user.is_active == true ? "Activo" : "Inactivo";
        document.getElementById("viewUserCreated").textContent = user.created_at ?? "-";

        // Badge de rol
        const roleBadge = document.getElementById("viewUserRoleBadge");
        roleBadge.textContent = user.RoleName;

    } catch (error) {
        console.error("Error:", error);
    }
}