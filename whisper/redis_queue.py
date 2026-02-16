import redis
import json
from typing import Any, Optional, Dict
from config import REDIS_URL, JOB_QUEUE, RESULT_PREFIX, RESULT_TTL_SECONDS

# Create Redis client that returns strings (not raw bytes).
# This makes working with JSON and text simpler.
r = redis.Redis.from_url(REDIS_URL, decode_responses=True)

def push_job(job: dict):
    """
    Push a job to the job queue.
    
    :param job: A dictionary containing job details, e.g. {"job_id": "123", "language": "en"}
    :type job: dict
    """
    # Ensure we push a JSON string onto the list
    r.lpush(JOB_QUEUE, json.dumps(job))

def pop_job() -> Optional[Dict[str, Any]]:
    """
    Pop a job from the job queue.

    This blocks until a job is available (BRPOP). Returns the job as a dict,
    or None if no job was returned (shouldn't happen with blocking BRPOP unless interrupted).
    """
    item = r.brpop(JOB_QUEUE)  # blocks until an item is available
    if not item:
        return None
    # brpop returns a tuple (queue_name, value) when decode_responses=True value is a string
    _, job_str = item
    try:
        return json.loads(job_str)
    except Exception:
        # If the payload isn't valid JSON, return it wrapped in a dict for compatibility
        return {"raw": job_str}

def set_result(job_id: str, result: Any):
    """
    Push a result for a job_id to Redis with TTL.
    
    :param job_id: The job identifier
    :type job_id: str
    :param result: The result to store (dict, string, etc.). If dict or list -> stored as JSON.
    :type result: Any
    """
    key = f"{RESULT_PREFIX}:{job_id}"
    # Serialize dicts/lists to JSON so Redis stores a string
    if isinstance(result, (dict, list)):
        payload = json.dumps(result)
    else:
        # Convert other types to string (int/float/str)
        payload = str(result)
    r.set(key, payload, ex=RESULT_TTL_SECONDS)

def get_result(job_id: str) -> Optional[Any]:
    """
    Get the result for a job_id from Redis.

    Returns the deserialized object (dict/list) if JSON was stored, otherwise returns the
    string value. Returns None if no result exists.
    
    :param job_id: The job identifier
    :type job_id: str
    """
    key = f"{RESULT_PREFIX}:{job_id}"
    val = r.get(key)
    if val is None:
        return None
    # Try to parse JSON, fall back to raw string
    try:
        return json.loads(val)
    except Exception:
        return val

def delete_result(job_id: str):
    """
    Delete a result for a job_id from Redis.
    
    :param job_id: The job identifier
    :type job_id: str
    """
    key = f"{RESULT_PREFIX}:{job_id}"
    r.delete(key)