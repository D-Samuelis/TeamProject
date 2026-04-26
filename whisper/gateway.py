from fastapi import FastAPI, UploadFile, File, HTTPException
import uuid, os
from redis_queue import push_job
from config import AUDIO_DIR
from pydub import AudioSegment

from redis_queue import get_result


app = FastAPI()
os.makedirs(AUDIO_DIR, exist_ok=True)

@app.get("/ping")
async def ping():
    return {"message": "pong"}

@app.get("/result/{job_id}")
async def get_transcription_result(job_id: str):
    result = get_result(job_id)
    if result is None:
        return {"status": "pending"}
    return result

@app.post("/transcribe")
async def transcribe(file: UploadFile = File(...), language: str = "sk"):
    if not file.filename:
        raise HTTPException(status_code=400, detail="No file uploaded")

    job_id = str(uuid.uuid4())
    orig_ext = os.path.splitext(file.filename)[1].lower()

    # Save original uploaded file
    orig_path = os.path.join(AUDIO_DIR, f"{job_id}{orig_ext}")
    try:
        with open(orig_path, "wb") as f:
            while chunk := await file.read(1024 * 1024):
                f.write(chunk)
    except Exception as e:
        if os.path.exists(orig_path):
            os.remove(orig_path)
        raise HTTPException(status_code=500, detail=f"Error saving uploaded file: {e}")

    # Convert to .wav
    wav_path = os.path.join(AUDIO_DIR, f"{job_id}.wav")
    try:
        audio = AudioSegment.from_file(orig_path)
        audio.export(wav_path, format="wav")
        print(f"[INFO] Saved WAV file: {wav_path}")
    except Exception as e:
        if os.path.exists(orig_path):
            os.remove(orig_path)
        raise HTTPException(status_code=500, detail=f"Error converting file to WAV: {e}")

    # Remove original file
    os.remove(orig_path)

    # Push the job
    push_job({"job_id": job_id, "language": language})

    return {"job_id": job_id}