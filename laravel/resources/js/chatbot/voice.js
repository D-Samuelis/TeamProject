import { transcribeAudio, pollTranscription } from "./api.js";

export function createVoiceRecorder({ onTranscript, onError, onStateChange }) {
    let mediaRecorder = null;
    let chunks = [];

    async function start() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            chunks = [];
            mediaRecorder = new MediaRecorder(stream, { mimeType: "audio/webm" });

            mediaRecorder.ondataavailable = e => {
                if (e.data.size > 0) chunks.push(e.data);
            };

            mediaRecorder.onstop = async () => {
                stream.getTracks().forEach(t => t.stop());
                const blob = new Blob(chunks, { type: "audio/webm" });
                onStateChange("transcribing");

                try {
                    const { job_id } = await transcribeAudio(blob);
                    const text = await pollTranscription(job_id);
                    onTranscript(text);
                } catch (err) {
                    onError(err.message);
                } finally {
                    onStateChange("idle");
                }
            };

            mediaRecorder.start();
            onStateChange("recording");
        } catch (err) {
            onError("Microphone access denied");
            onStateChange("idle");
        }
    }

    function stop() {
        if (mediaRecorder?.state === "recording") {
            mediaRecorder.stop();
        }
    }

    function isRecording() {
        return mediaRecorder?.state === "recording";
    }

    return { start, stop, isRecording };
}
