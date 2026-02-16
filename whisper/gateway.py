from fastapi import FastAPI, UploadFile, File, HTTPException
import uuid, os
from redis_queue import push_job
from config import AUDIO_DIR
from pydub import AudioSegment  # convert audio formats

app = FastAPI()
os.makedirs(AUDIO_DIR, exist_ok=True)

@app.get("/ping")
async def ping():
    return {"message": "pong"}

@app.post("/transcribe")
async def transcribe(file: UploadFile = File(...), language: str = "sk"):
    if not file.filename:
        raise HTTPException(status_code=400, detail="No file uploaded")

    job_id = str(uuid.uuid4())
    orig_ext = os.path.splitext(file.filename)[1].lower()

    # Save original uploaded file first
    orig_path = os.path.join(AUDIO_DIR, f"{job_id}{orig_ext}")
    try:
        with open(orig_path, "wb") as f:
            while chunk := await file.read(1024 * 1024):
                f.write(chunk)
    except Exception as e:
        if os.path.exists(orig_path):
            os.remove(orig_path)
        raise HTTPException(status_code=500, detail=f"Error saving uploaded file: {e}")

    # Convert to WAV for the worker
    wav_path = os.path.join(AUDIO_DIR, f"{job_id}.wav")
    try:
        audio = AudioSegment.from_file(orig_path)
        audio.export(wav_path, format="wav")
        print(f"[INFO] Saved WAV file: {wav_path}")
    except Exception as e:
        if os.path.exists(orig_path):
            os.remove(orig_path)
        raise HTTPException(status_code=500, detail=f"Error converting file to WAV: {e}")

    # Optional: remove original file to save space
    os.remove(orig_path)

    # Push job to Redis
    push_job({"job_id": job_id, "language": language})

    return {"job_id": job_id}



""" from fastapi import FastAPI, UploadFile, File, HTTPException
import uuid, os, asyncio
from redis_queue import push_job
from config import AUDIO_DIR

app = FastAPI()
os.makedirs(AUDIO_DIR, exist_ok=True)

@app.get("/ping")
async def ping():
    return {"message": "pong"}

@app.post("/transcribe")
async def transcribe(file: UploadFile = File(...), language: str = "sk"):
    if not file.filename:
        raise HTTPException(status_code=400, detail="No file uploaded")

    job_id = str(uuid.uuid4())
    file_path = os.path.join(AUDIO_DIR, f"{job_id}.wav")

    # Save file in streaming fashion to avoid big memory spike
    try:
        with open(file_path, "wb") as f:
            while True:
                chunk = await file.read(1024 * 1024)
                if not chunk:
                    break
                f.write(chunk)
    except Exception as e:
        # cleanup and return error
        if os.path.exists(file_path):
            os.remove(file_path)
        raise HTTPException(status_code=500, detail=f"Error saving file: {e}")

    # Push the job metadata (worker will pick it up)
    push_job({"job_id": job_id, "language": language})

    # Return job_id immediately (asynchronous flow)
    return {"job_id": job_id}

if __name__ == "__main__":
    import uvicorn
    uvicorn.run("gateway:app", host="0.0.0.0", port=8000, log_level="info") """