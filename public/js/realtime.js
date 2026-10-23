/**
 * Cliente SSE (Server-Sent Events) para tiempo real
 * Auto-reconexión, manejo de eventos, tipos de mensajes
 */

class RealtimeClient {
    constructor(url, options = {}) {
        this.url = url;
        this.eventSource = null;
        this.reconnectAttempts = 0;
        this.maxReconnectAttempts = options.maxReconnectAttempts || 10;
        this.reconnectDelay = options.reconnectDelay || 3000;
        this.handlers = new Map();
        this.connected = false;
    }

    connect() {
        // En dev local (php spark serve / single-threaded) el SSE congelaría el servidor
        if (window.__sseDisponible === false) {
            console.log('Realtime: SSE deshabilitado en este entorno (dev local). Usar polling.');
            this.emit('sse_disabled');
            return;
        }
        if (this.eventSource) this.disconnect();
        
        this.eventSource = new EventSource(this.url);
        
        this.eventSource.onopen = () => {
            this.connected = true;
            this.reconnectAttempts = 0;
            this.emit('connect');
        };

        this.eventSource.onerror = (err) => {
            this.connected = false;
            this.emit('error', err);
            this.scheduleReconnect();
        };

        // Handlers por tipo de evento
        this.addEventListener('message', (e) => this.handleMessage(e));
        this.addEventListener('asistencia_nueva', (e) => this.emit('asistencia_nueva', e.data));
        this.addEventListener('asistencia_editada', (e) => this.emit('asistencia_editada', e.data));
        this.addEventListener('asistencia_actualizada', (e) => this.emit('asistencia_actualizada', e.data));
        this.addEventListener('alerta', (e) => this.emit('alerta', e.data));
    }

    handleMessage(event) {
        try {
            const data = JSON.parse(event.data);
            this.emit(data.tipo, data);
        } catch (e) {
            console.warn('Realtime: mensaje no JSON', event.data);
        }
    }

    addEventListener(type, handler) {
        if (!this.handlers.has(type)) this.handlers.set(type, new Set());
        this.handlers.get(type).add(handler);
        
        // También registrar en EventSource nativo
        if (this.eventSource) {
            this.eventSource.addEventListener(type, (e) => handler(e));
        }
    }

    on(type, handler) {
        this.addEventListener(type, handler);
    }

    emit(type, data) {
        if (this.handlers.has(type)) {
            this.handlers.get(type).forEach(h => h(data));
        }
    }

    scheduleReconnect() {
        if (this.reconnectAttempts >= this.maxReconnectAttempts) {
            this.emit('max_reconnect_failed');
            return;
        }
        
        this.reconnectAttempts++;
        const delay = this.reconnectDelay * Math.pow(1.5, this.reconnectAttempts - 1);
        console.log(`Realtime: reconectando en ${delay}ms (intento ${this.reconnectAttempts})`);
        
        setTimeout(() => this.connect(), delay);
    }

    disconnect() {
        if (this.eventSource) {
            this.eventSource.close();
            this.eventSource = null;
            this.connected = false;
        }
    }

    isConnected() {
        return this.connected;
    }
}

// Auto-inicialización para páginas comunes
document.addEventListener('DOMContentLoaded', () => {
    // Admin: asistencias en tiempo real
    if (window.location.pathname.startsWith('/admin/asistencias')) {
        window.realtimeAdmin = new RealtimeClient('/admin/realtime/asistencias');
        window.realtimeAdmin.on('asistencia_nueva', (data) => {
            console.log('Nueva asistencia:', data);
            // Agregar fila a la tabla sin recargar
            window.agregarFilaAsistencia(data.datos);
        });
        window.realtimeAdmin.on('asistencia_editada', (data) => {
            window.actualizarFilaAsistencia(data.datos);
        });
        window.realtimeAdmin.connect();
    }

    // Empleado: su panel en tiempo real
    if (window.location.pathname.startsWith('/mi-panel')) {
        window.realtimeEmpleado = new RealtimeClient('/mi-panel/realtime/asistencias');
        window.realtimeEmpleado.on('asistencia_actualizada', (data) => {
            window.actualizarUIAsistencia(data.asistencia);
        });
        window.realtimeEmpleado.on('alerta', (data) => {
            window.mostrarAlerta(data.datos);
        });
        window.realtimeEmpleado.connect();
    }
});

// Helper global para reconexión manual
window.RealtimeClient = RealtimeClient;
