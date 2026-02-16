import os
import time
import traceback
from faster_whisper import WhisperModel
from redis_queue import pop_job, set_result
from audio import preprocess
from config import AUDIO_DIR

os.makedirs(AUDIO_DIR, exist_ok=True)

# Choose model size deliberately; ensure image predownload matches or remove predownload.
MODEL_NAME = "medium"
model = WhisperModel(MODEL_NAME, compute_type="int8")
print(f"Whisper model {MODEL_NAME} loaded.")

WAIT_FOR_RAW_SECONDS = 5.0
SLEEP_STEP = 0.1

while True:
    raw_path = None
    clean_path = None
    try:
        data = pop_job()  # blocks until job available
        job_id = data.get("job_id")
        if not job_id:
            print("[worker] invalid job payload:", data)
            continue
        language = data.get("language", None)

        raw_path = os.path.join(AUDIO_DIR, f"{job_id}.wav")
        clean_path = os.path.join(AUDIO_DIR, f"{job_id}_clean.wav")

        # Wait for gateway to flush the file (defensive)
        waited = 0.0
        while not os.path.exists(raw_path) and waited < WAIT_FOR_RAW_SECONDS:
            time.sleep(SLEEP_STEP)
            waited += SLEEP_STEP

        if not os.path.exists(raw_path):
            print(f"[worker] raw file missing for {job_id}, skipping.")
            set_result(job_id, {
                "status": "error",
                "stage": "input",
                "message": f"Audio file not found for job_id {job_id}"
            })
            continue

        # Preprocess
        try:
            preprocess(raw_path, clean_path)
        except Exception as e:
            print(f"[worker] preprocess failed for {job_id}: {e}")
            set_result(job_id, {
                "status": "error",
                "stage": "preprocess",
                "message": str(e)
            })
            continue

        # Defensive: make sure clean file exists
        waited = 0.0
        while not os.path.exists(clean_path) and waited < 2.0:
            time.sleep(SLEEP_STEP)
            waited += SLEEP_STEP

        if not os.path.exists(clean_path):
            print(f"[worker] clean file missing for {job_id} after preprocess, skipping.")
            set_result(job_id, {
                "status": "error",
                "stage": "preprocess",
                "message": f"Clean file missing after preprocessing for job_id {job_id}"
            })
            continue

        # Transcribe
        try:
            segments, info = model.transcribe(clean_path, task="transcribe", language=language)
            segments = list(segments)
            text = " ".join(s.text for s in segments).strip()
            set_result(job_id, {
                "status": "success",
                "text": text
            })
            print(f"[worker] job {job_id} done, text_len={len(text)}")
        except Exception as e:
            tb = traceback.format_exc()
            print(f"[worker] transcription error for {job_id}: {e}\n{tb}")
            set_result(job_id, {
                "status": "error",
                "stage": "transcribe",
                "message": str(e)
            })

    except Exception as outer:
        print(f"[worker] top level loop error: {outer}")
        time.sleep(1.0)
    finally:
        # Try cleanup of both files, ignore failures
        try:
            if os.path.exists(raw_path): os.remove(raw_path)
            if os.path.exists(clean_path): os.remove(clean_path)
        except Exception:
            pass