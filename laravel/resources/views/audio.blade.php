<!DOCTYPE html>
<html>
<head>
  <title>Audio Transcription</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="session-id" content="{{ session()->getId() }}">
</head>
<body>
  <h1>Continuous Audio Recording</h1>
  <button id="start">Start Recording</button>
  <button id="stop" disabled>Stop & Transcribe</button>

  <h2>Transcript:</h2>
  <div id="transcript">Waiting...</div>

  <script>
    let mediaRecorder = null;
    let audioChunks = [];
    let stream = null;

    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    const sessionId = document.querySelector('meta[name="session-id"]').content;
    const transcriptDiv = document.getElementById('transcript');
    const startBtn = document.getElementById('start');
    const stopBtn = document.getElementById('stop');

    startBtn.onclick = async () => {
      transcriptDiv.innerText = "Recording...";
      audioChunks = [];

      try {
        stream = await navigator.mediaDevices.getUserMedia({ audio: true });

        // Choose an explicit mimeType that browsers support (check support first)
        const mime = MediaRecorder.isTypeSupported('audio/webm;codecs=opus')
                    ? 'audio/webm;codecs=opus'
                    : (MediaRecorder.isTypeSupported('audio/webm') ? 'audio/webm' : '');

        mediaRecorder = mime ? new MediaRecorder(stream, { mimeType: mime }) : new MediaRecorder(stream);

        mediaRecorder.addEventListener('dataavailable', e => {
          if (e.data && e.data.size > 0) audioChunks.push(e.data);
        });

        mediaRecorder.addEventListener('stop', async () => {
          transcriptDiv.innerText = "Uploading & transcribing...";

          // Combine into ONE final audio file
          const audioBlob = new Blob(audioChunks, { type: mediaRecorder.mimeType || 'audio/webm' });

          // Stop mic
          stream.getTracks().forEach(t => t.stop());

          const formData = new FormData();
          formData.append('audio', audioBlob, 'recording.webm');
          formData.append('session_id', sessionId);

          try {
            const res = await fetch('/audio/transcribe', {
              method: 'POST',
              headers: { 'X-CSRF-TOKEN': csrf },
              body: formData
            });

            if (!res.ok) {
              const text = await res.text();
              console.error('Upload failed', res.status, text);
              transcriptDiv.innerText = "Error uploading audio.";
              return;
            }

            const data = await res.json();
            transcriptDiv.innerText = data.text || "No transcript returned.";
          } catch (err) {
            transcriptDiv.innerText = "Error uploading audio.";
            console.error(err);
          }
        });

        mediaRecorder.start(); // continuous
        startBtn.disabled = true;
        stopBtn.disabled = false;
      } catch (err) {
        transcriptDiv.innerText = "Microphone access denied.";
        console.error(err);
      }
    };

    stopBtn.onclick = () => {
      if (mediaRecorder && mediaRecorder.state !== "inactive") mediaRecorder.stop();
      startBtn.disabled = false;
      stopBtn.disabled = true;
    };
  </script>
</body>
</html>
