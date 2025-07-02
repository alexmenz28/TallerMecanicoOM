// Lógica para el historial de servicios
document.addEventListener('DOMContentLoaded', function() {
    const historialForm = document.getElementById('historialForm');
    const placaInput = document.getElementById('placaBuscar');
    const resultadosDiv = document.getElementById('resultados');
    const sinResultadosDiv = document.getElementById('sinResultados');

    // Auto-formatear placa en búsqueda
    placaInput.addEventListener('input', function() {
        let value = this.value.toUpperCase();
        // Remover caracteres no válidos
        value = value.replace(/[^A-Z0-9]/g, '');
        // Limitar a 6 caracteres
        value = value.substring(0, 6);
        this.value = value;
    });

    // Manejar búsqueda del historial
    historialForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const placa = placaInput.value.trim();
        
        if (!placa) {
            alert('Por favor ingresa la placa del vehículo.');
            return;
        }

        // Validar formato de placa
        const placaRegex = /^[A-Z]{3}\d{3}$/;
        if (!placaRegex.test(placa.toUpperCase())) {
            alert('Por favor ingresa una placa válida (formato: ABC123).');
            return;
        }

        // Mostrar indicador de carga
        mostrarCargando();

        // Simular búsqueda (en producción esto sería una llamada AJAX)
        setTimeout(() => {
            buscarHistorial(placa.toUpperCase());
        }, 1000);
    });

    function mostrarCargando() {
        resultadosDiv.style.display = 'none';
        sinResultadosDiv.style.display = 'none';
        
        // Crear indicador de carga si no existe
        let loadingDiv = document.getElementById('loading');
        if (!loadingDiv) {
            loadingDiv = document.createElement('div');
            loadingDiv.id = 'loading';
            loadingDiv.className = 'loading';
            loadingDiv.innerHTML = `
                <div class="spinner"></div>
                <p>Buscando historial...</p>
            `;
            document.querySelector('main').appendChild(loadingDiv);
        }
        loadingDiv.style.display = 'block';
    }

    function ocultarCargando() {
        const loadingDiv = document.getElementById('loading');
        if (loadingDiv) {
            loadingDiv.style.display = 'none';
        }
    }

    function buscarHistorial(placa) {
        // En producción, aquí se haría una llamada AJAX a php/buscar_historial.php
        // Por ahora, simulamos datos de ejemplo
        
        // Simular datos de ejemplo
        const datosEjemplo = {
            'ABC123': {
                vehiculo: {
                    placa: 'ABC123',
                    marca: 'Toyota',
                    modelo: 'Corolla 2020'
                },
                servicios: [
                    {
                        id: 1,
                        fecha: '2024-01-15',
                        servicio: 'Cambio de aceite',
                        descripcion: 'Cambio de aceite sintético y filtro',
                        estado: 'Completado'
                    },
                    {
                        id: 2,
                        fecha: '2024-02-20',
                        servicio: 'Revisión de frenos',
                        descripcion: 'Cambio de pastillas de freno delanteras',
                        estado: 'Completado'
                    },
                    {
                        id: 3,
                        fecha: '2024-03-10',
                        servicio: 'Diagnóstico de motor',
                        descripcion: 'Revisión de códigos de error',
                        estado: 'Completado'
                    }
                ]
            }
        };

        ocultarCargando();

        if (datosEjemplo[placa]) {
            mostrarResultados(datosEjemplo[placa]);
        } else {
            mostrarSinResultados();
        }
    }

    function mostrarResultados(datos) {
        const vehiculo = datos.vehiculo;
        const servicios = datos.servicios;

        // Actualizar información del vehículo
        document.getElementById('placaVehiculo').textContent = vehiculo.placa;
        document.getElementById('marcaVehiculo').textContent = vehiculo.marca;
        document.getElementById('modeloVehiculo').textContent = vehiculo.modelo;

        // Actualizar estadísticas
        document.getElementById('totalServicios').textContent = servicios.length;
        document.getElementById('ultimoServicio').textContent = servicios[0].fecha;
        
        // Calcular servicio más frecuente
        const serviciosCount = {};
        servicios.forEach(s => {
            serviciosCount[s.servicio] = (serviciosCount[s.servicio] || 0) + 1;
        });
        const servicioFrecuente = Object.keys(serviciosCount).reduce((a, b) => 
            serviciosCount[a] > serviciosCount[b] ? a : b
        );
        document.getElementById('servicioFrecuente').textContent = servicioFrecuente;

        // Generar lista de servicios
        const listaServicios = document.getElementById('listaServicios');
        listaServicios.innerHTML = '';

        servicios.forEach(servicio => {
            const servicioDiv = document.createElement('div');
            servicioDiv.className = 'servicio-item';
            servicioDiv.innerHTML = `
                <div class="servicio-header">
                    <h4>${servicio.servicio}</h4>
                    <span class="fecha">${formatearFecha(servicio.fecha)}</span>
                </div>
                <p class="descripcion">${servicio.descripcion}</p>
                <span class="estado ${servicio.estado.toLowerCase()}">${servicio.estado}</span>
            `;
            listaServicios.appendChild(servicioDiv);
        });

        resultadosDiv.style.display = 'block';
        sinResultadosDiv.style.display = 'none';
    }

    function mostrarSinResultados() {
        resultadosDiv.style.display = 'none';
        sinResultadosDiv.style.display = 'block';
    }

    function formatearFecha(fecha) {
        const opciones = { 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        };
        return new Date(fecha).toLocaleDateString('es-ES', opciones);
    }
}); 