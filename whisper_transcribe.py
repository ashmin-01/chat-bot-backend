import whisper
import sys

def transcribe_audio(file_path):
    try:
        model = whisper.load_model("base")
        result = model.transcribe(file_path)
        return result["text"]
    except ImportError as e:
        return f"Import error: {e}"
    except OSError as e:
        return f"OS error: {e}"
    except Exception as e:
        return f"An unexpected error occurred: {e}"

if __name__ == "__main__":
    if len(sys.argv) != 2:
        print("Usage: python whisper_transcribe.py <file_path>")
        sys.exit(1)
    
    file_path = sys.argv[1]
    transcription = transcribe_audio(file_path)
    print(transcription)
