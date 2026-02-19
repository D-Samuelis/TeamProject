import os

REDIS_URL = os.getenv("REDIS_URL", "redis://redis:6379/0")
JOB_QUEUE = os.getenv("JOB_QUEUE", "stt:queue:jobs")
RESULT_PREFIX = os.getenv("RESULT_PREFIX", "stt:result")
RESULT_TTL_SECONDS = int(os.getenv("RESULT_TTL_SECONDS", "3600")) # keep result for 1 hour, remove later(?)
AUDIO_DIR = os.getenv("AUDIO_DIR", "/tmp/audio")
TARGET_SAMPLE_RATE = int(os.getenv("TARGET_SAMPLE_RATE", "16000"))