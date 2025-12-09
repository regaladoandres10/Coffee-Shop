
const API_BASE_URL = '/public/api.php';

/**
 * Función genérica para obtener y renderizar datos.
 * @param {string} resource - El recurso de la API (ej: 'products').
 * @param {string} targetTableId - ID de la tabla HTML donde renderizar.
 */
async function loadData(resource, targetTableId) {
    try {
        const response = await fetch(`${API_BASE_URL}?resource=${resource}`);
        const result = await response.json();

        const tableBody = document.getElementById(targetTableId);
        tableBody.innerHTML = ''; // Limpiar contenido existente

        if (result.success && result.data.length > 0) {
            result.data.forEach(item => {
                const row = document.createElement('tr');
                // EJEMPLO para productos
                row.innerHTML = `
                    <td>${item.idProduct || item.idCategorie}</td>
                    <td>${item.name}</td>
                    <td>${item.price ? '$' + item.price : item.description}</td>
                    <td>${item.isAvailable ? 'Sí' : 'No'}</td>
                    <td>
                        <button onclick="editItem('${resource}', ${item.idProduct || item.idCategorie})" class="btn btn-sm btn-info">Editar</button>
                        <button onclick="deleteItem('${resource}', ${item.idProduct || item.idCategorie})" class="btn btn-sm btn-danger">Eliminar</button>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        } else {
            tableBody.innerHTML = `<tr><td colspan="5" class="text-center">${result.message || 'No hay datos para mostrar.'}</td></tr>`;
        }
    } catch (error) {
        console.error('Error al cargar datos:', error);
    }
}