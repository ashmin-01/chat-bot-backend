from fastapi import FastAPI, UploadFile, File, HTTPException, Form
from pydantic import BaseModel
from dotenv import load_dotenv
from langchain_text_splitters import RecursiveCharacterTextSplitter
from langchain_community.document_loaders import BSHTMLLoader
from langchain_community.embeddings import HuggingFaceInferenceAPIEmbeddings
from langchain_core.prompts import PromptTemplate
from langchain_chroma import Chroma
import tempfile
import os

load_dotenv(dotenv_path='.env.langchain')

hugging_api_key = os.getenv('HUGGING_FACE_API_KEY')

folder_path = "D:\\welcome\\welcome\\IT\\Modified Articles"

# List all files in the folder
file_paths = [os.path.join(folder_path, file) for file in os.listdir(folder_path) if file.endswith('.html')]

# Load each HTML file and store the documents
docs = []
for file_path in file_paths:
    loader = BSHTMLLoader(file_path, open_encoding="utf-8")
    docs.extend(loader.load())

text_splitter = RecursiveCharacterTextSplitter(chunk_size=1000, chunk_overlap=200)
splits = text_splitter.split_documents(docs)

embedder = HuggingFaceInferenceAPIEmbeddings(
    api_key=hugging_api_key, model_name="AbderrahmanSkiredj1/Arabic_text_embedding_for_sts"
)

persist_directory = 'vector db'

vectorstore = Chroma.from_documents(documents=splits, embedding=embedder, persist_directory=persist_directory)

app = FastAPI()

class Config(BaseModel):
    embedding_model_name: str
    api_key: str
    k: int
    text_splitter_chunk_size: int
    text_splitter_chunk_overlap: int
    prompt_template: str


# Initialize global variables
vectorstore = None
text_splitter = None
rag_pipeline = None
current_config = None

@app.post("/configure")
async def configure(
    embedding_model_name: str = Form(...),
    api_key: str = Form(...),
    k: int = Form(...),
    text_splitter_chunk_size: int = Form(...),
    text_splitter_chunk_overlap: int = Form(...),
    prompt_template: str = Form(...)
):
    global vectorstore, rag_pipeline , text_splitter, current_config

    try:
        # Initialize embeddings and vector store
        embedder = HuggingFaceInferenceAPIEmbeddings(api_key=api_key, model_name=embedding_model_name)
        persist_directory = 'vector db'
        vectorstore = Chroma(persist_directory=persist_directory, embedding_function=embedder)

        # Initialize text splitter
        text_splitter = RecursiveCharacterTextSplitter(chunk_size=text_splitter_chunk_size, chunk_overlap=text_splitter_chunk_overlap)

        # Initialize prompt template
        prompt_template_instance = PromptTemplate.from_template(prompt_template)

        current_config = {
            "embedding_model_name": embedding_model_name,
            "api_key": api_key,
            "k": k,
            "text_splitter_chunk_size": text_splitter_chunk_size,
            "text_splitter_chunk_overlap": text_splitter_chunk_overlap,
            "prompt_template": prompt_template
        }

        return {
            "status": "success",
            "message": "Configuration updated successfully.",
            "embedding_model_name": embedding_model_name,
            "api_key": api_key,
            "k": k,
            "text_splitter_chunk_size": text_splitter_chunk_size,
            "text_splitter_chunk_overlap": text_splitter_chunk_overlap,
            "prompt_template": prompt_template
        }
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))

@app.get("/configuration")
async def get_configuration():
    if current_config is None:
        raise HTTPException(status_code=400, detail="Configuration not set")
    return current_config

@app.post("/upload")
async def upload_file(file: UploadFile = File(...)):
    global vectorstore, text_splitter

    if vectorstore is None or text_splitter is None:
        raise HTTPException(status_code=400, detail="Configuration not set")

    try:
        # Use tempfile to handle temporary file creation
        with tempfile.NamedTemporaryFile(delete=False, suffix=".html") as temp_file:
            contents = await file.read()
            temp_file.write(contents)
            temp_file_path = temp_file.name

        # Process the HTML file
        loader = BSHTMLLoader(temp_file_path, open_encoding="utf-8")
        docs = loader.load()

        # Split documents into chunks
        splits = text_splitter.split_documents(docs)

        # Update the vector store with new documents
        vectorstore.add_documents(documents=splits, embedding=embedder)

        return {"status": "success", "message": f"File '{file.filename}' uploaded and processed successfully."}
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))

if __name__ == '__main__':
    import uvicorn
    uvicorn.run(app, host='0.0.0.0', port=8002)
