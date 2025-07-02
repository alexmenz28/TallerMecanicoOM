// Lógica para el formulario de agendar citas
document.addEventListener('DOMContentLoaded', function() {
    const citaForm = document.getElementById('citaForm');
    const fechaInput = document.getElementById('fecha');
    const horaSelect = document.getElementById('hora');

    // Establecer fecha mínima como hoy
    const today = new Date().toISOString().split('T')[0];
    fechaInput.min = today;

    // Validar que no se seleccione domingo
    fechaInput.addEventListener('change', function() {
        const selectedDate = new Date(this.value);
        const dayOfWeek = selectedDate.getDay();
        
        if (dayOfWeek === 0) { // Domingo
            alert('No trabajamos los domingos. Por favor selecciona otro día.');
            this.value = '';
        }
    });

    // Validar horario de trabajo
    horaSelect.addEventListener('change', function() {
        const selectedHour = this.value;
        const hour = parseInt(selectedHour.split(':')[0]);
        
        if (hour < 8 || hour > 17) {
            alert('Nuestro horario de trabajo es de 8:00 AM a 5:00 PM');
            this.value = '';
        }
    });

    // Manejar envío del formulario
    citaForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validaciones adicionales
        const nombre = document.getElementById('nombre').value.trim();
        const telefono = document.getElementById('telefono').value.trim();
        const placa = document.getElementById('placa').value.trim();
        const marca = document.getElementById('marca').value.trim();
        const modelo = document.getElementById('modelo').value.trim();
        const servicio = document.getElementById('servicio').value;
        const fecha = document.getElementById('fecha').value;
        const hora = document.getElementById('hora').value;

        // Validar campos requeridos
        if (!nombre || !telefono || !placa || !marca || !modelo || !servicio || !fecha || !hora) {
            alert('Por favor completa todos los campos requeridos.');
            return;
        }

        // Validar formato de teléfono
        const phoneRegex = /^\d{10}$/;
        if (!phoneRegex.test(telefono.replace(/\s/g, ''))) {
            alert('Por favor ingresa un número de teléfono válido (10 dígitos).');
            return;
        }

        // Validar formato de placa (formato colombiano)
        const placaRegex = /^[A-Z]{3}\d{3}$/;
        if (!placaRegex.test(placa.toUpperCase())) {
            alert('Por favor ingresa una placa válida (formato: ABC123).');
            return;
        }

        // Mostrar confirmación
        const confirmacion = confirm(`
            ¿Confirmar cita?
            
            Nombre: ${nombre}
            Teléfono: ${telefono}
            Placa: ${placa.toUpperCase()}
            Vehículo: ${marca} ${modelo}
            Servicio: ${servicio}
            Fecha: ${fecha}
            Hora: ${hora}
        `);

        if (confirmacion) {
            // Enviar formulario
            this.submit();
        }
    });

    // Auto-formatear placa
    const placaInput = document.getElementById('placa');
    placaInput.addEventListener('input', function() {
        let value = this.value.toUpperCase();
        // Remover caracteres no válidos
        value = value.replace(/[^A-Z0-9]/g, '');
        // Limitar a 6 caracteres
        value = value.substring(0, 6);
        this.value = value;
    });

    // Auto-formatear teléfono
    const telefonoInput = document.getElementById('telefono');
    telefonoInput.addEventListener('input', function() {
        let value = this.value.replace(/\D/g, '');
        // Limitar a 10 dígitos
        value = value.substring(0, 10);
        this.value = value;
    });
}); 