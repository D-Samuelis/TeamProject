from pydub import AudioSegment, effects
from pydub.silence import detect_nonsilent
from config import TARGET_SAMPLE_RATE

MIN_SILENCE_LEN_MS = 500
SILENCE_THRESH_DBFS = -40

def trim_silence(audio: AudioSegment, silence_len=MIN_SILENCE_LEN_MS, silence_thresh=SILENCE_THRESH_DBFS) -> AudioSegment:
    nonsilent_ranges = detect_nonsilent(audio, min_silence_len=silence_len, silence_thresh=silence_thresh)
    if not nonsilent_ranges:
        return audio
    start_ms = nonsilent_ranges[0][0]
    end_ms = nonsilent_ranges[-1][1]
    return audio[start_ms:end_ms]

def preprocess(input_path: str, output_path: str) -> str:
    """
    Preprocess audio for Whisper:
    - Convert to mono
    - Resample to TARGET_SAMPLE_RATE
    - Normalize volume
    - Trim leading/trailing silence (using detect_nonsilent)
    - Export WAV
    """
    audio = AudioSegment.from_file(input_path)

    # Convert to mono, resample
    audio = audio.set_channels(1)
    audio = audio.set_frame_rate(TARGET_SAMPLE_RATE)

    # Normalize volume
    audio = effects.normalize(audio)

    # Trim leading/trailing silence
    trimmed = trim_silence(audio)

    # Cleaned file .wav
    trimmed.export(output_path, format="wav")
    return output_path
