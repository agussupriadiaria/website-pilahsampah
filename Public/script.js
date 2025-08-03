document.addEventListener("DOMContentLoaded", async function () {
    const resultElement = document.getElementById("result");
    const cameraSelection = document.getElementById("camera-selection");
    let currentStream;

    // List available cameras
    async function listCameras() {
        const devices = await navigator.mediaDevices.enumerateDevices();
        const videoDevices = devices.filter((device) => device.kind === "videoinput");

        cameraSelection.innerHTML = ""; // Clear existing options

        videoDevices.forEach((device, index) => {
            const option = document.createElement("option");
            option.value = device.deviceId;
            option.textContent = device.label || `Camera ${index + 1}`;
            cameraSelection.appendChild(option);
        });
    }

    // Start camera with the selected device
    async function startCamera(deviceId) {
        if (currentStream) {
            currentStream.getTracks().forEach((track) => track.stop());
        }

        const constraints = {
            video: {
                deviceId: deviceId ? { exact: deviceId } : undefined,
            },
        };

        currentStream = await navigator.mediaDevices.getUserMedia(constraints);
        document.querySelector("#camera").srcObject = currentStream;

        Quagga.init(
            {
                inputStream: {
                    type: "LiveStream",
                    target: document.querySelector("#camera"),
                    constraints: constraints.video,
                },
                decoder: {
                    readers: ["code_128_reader", "ean_reader", "upc_reader"],
                },
            },
            function (err) {
                if (err) {
                    console.error(err);
                    resultElement.textContent = "Camera initialization failed.";
                    return;
                }
                Quagga.start();
            }
        );

        Quagga.onDetected((data) => {
            resultElement.textContent = `Detected: ${data.codeResult.code}`;
        });
    }

    // Handle camera change
    cameraSelection.addEventListener("change", () => {
        startCamera(cameraSelection.value);
    });

    // Initialize cameras and start the first one
    await listCameras();
    if (cameraSelection.options.length > 0) {
        startCamera(cameraSelection.value);
    }
});
