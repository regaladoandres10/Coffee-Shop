document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".edit-user-btn").forEach(btn => {
        btn.addEventListener("click", async function () {
            const userId = this.dataset.id;

            

            try {
                const response = await fetch(`http://localhost:3000/public/api.php?resource=users&id=${userId}`);
                const result = await response.json();

                if (!result.success) {
                    console.error("API error:", result.message);
                    return;
                }

                const user = result.data;

                //LLENAR MODAL
                document.getElementById("editUserId").value = user.idUser;
                document.getElementById("editName").value = user.name;
                document.getElementById("editUsername").value = user.nameUser;
                document.getElementById("editPhone").value = user.phone;
                document.getElementById("editRole").value = user.idRol;
                document.getElementById("editIsActive").value = user.is_active;

            } catch (error) {
                console.error("Error al cargar el usuario:", error);
            }
        });
    });
});

//Enviar formulario de actualización
document.addEventListener("DOMContentLoaded", () => {

    const editForm = document.getElementById("editUserForm");

    if (!editForm) {
        console.error("No se encontró el formulario de edición");
        return;
    }

    editForm.addEventListener("submit", async function (e) {
        e.preventDefault();
        console.log("Evento submit detectado correctamente");
        
        const userId = document.getElementById("editUserId").value;

    const formData = {
        id: userId,
        name: document.getElementById("editName").value,
        nameUser: document.getElementById("editUsername").value,
        phone: document.getElementById("editPhone").value,
        role: document.getElementById("editRole").value,
        is_active: document.getElementById("editIsActive").value,
    };

    const password = document.getElementById("editPassword").value.trim();
    if (password.length > 0) {
        formData.password = password;
    }

    try {
        const response = await fetch(`http://localhost:3000/public/api.php?resource=users&id=${userId}`, {
            method: "PUT",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(formData)
        });

        const result = await response.json();
        console.log("Respuesta del servidor:", result);

        if (result.success) {
            alert("Usuario actualizado correctamente");
            location.reload();
        } else {
            alert("Error al actualizar: " + result.message);
        }

    } catch (error) {
        console.error("Error al enviar actualización:", error);
    }    

    });

    
});


function editUserFromView(btn) {
    const userId = btn.dataset.id;

    // cargar datos
    loadUserData(userId);

    // abrir modal
    const modal = new bootstrap.Modal(document.getElementById('editUserModal'));
    modal.show();
}
