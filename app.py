from fastapi import FastAPI, Request, HTTPException
from pydantic import BaseModel
from dotenv import load_dotenv
import httpx
import os
#from langchain.vectorstores import Chroma
from langchain_community.document_loaders import BSHTMLLoader
from langchain_text_splitters import RecursiveCharacterTextSplitter
from langchain_community.embeddings import HuggingFaceInferenceAPIEmbeddings
from langchain_chroma import Chroma
from langchain_core.prompts import PromptTemplate
from groq import Groq
import cohere

load_dotenv(dotenv_path='.env.langchain')

cohere_api_key = os.getenv('COHERE_API_KEY')

dashboard_url = "http://localhost:8002/configuration"

app = FastAPI()

class RAGPipeline:
    def __init__(self, vectorstore, api_key, model_name="gemma2-9b-it", k=6):
        self.vectorstore = vectorstore
        self.api_key = api_key
        self.k = k
        self.retriever = self.vectorstore.as_retriever(search_type="similarity", search_kwargs={"k": self.k})
        self.prompt_template = PromptTemplate.from_template(self._get_template())
        self.co = cohere.Client(api_key=cohere_api_key)
        self.chat_history = []

    def _get_template(self):
        return """
        قم بفهم السياقات التالية ثم بقم بالاجابة على الأسئلة.
        أجب باللغة العربية فقط.
        إذا كنت لا تعرف الإجابة، فقط قل إنك لا تعرف، لا تحاول تصنيع إجابة.
        حافظ على إجابتك شاملة وصحيحة ومختصرة قدر الإمكان.
        أضف مقدمة مناسبة تشرح للزبون ماهية سؤاله و ماهية الجواب.
        اقترح أسئلة من المعلومات في حال كان السؤال قريب من المعلومات لكنه خاطئ.
        كن لبقا في إجاباتك.
        استخدم السياقات التالية للإجابة على السؤال في النهاية.
        \n السياق: {context}
        \n السؤال: {question}
        \n الإجابة المفيدة:
        """

    def generate_response(self, question):
        try:
            retrieved_docs = self._retrieve_documents(question)
            prompt = self._create_prompt(retrieved_docs, question)
            response = self._query_model(prompt)
            self._update_chat_history(question, response)
            return response
        except Exception as e:
            return f"Error generating response: {e}"

    def _retrieve_documents(self, question):
        try:
            retrieved_docs = self.retriever.invoke(question)
            return {f'doc_{i}': doc.page_content for i, doc in enumerate(retrieved_docs)}
        except Exception as e:
            raise ValueError(f"Error retrieving documents: {e}")

    def _create_prompt(self, docs, question):
        return self.prompt_template.format(context=docs, question=question)

    def _query_model(self, message):
        try:
            response = self.co.chat_stream(
                model="command-r-plus",
                message=message,
                preamble="أنت شات بوت تعمل كموظف خدمة زبائن لدى سيرياتيل.",
                chat_history=self.chat_history,
                max_tokens=1500, # max number of generated tokens
                temperature=0.3, # Higher temperatures mean more random generations.
            )
            complete_response = ""
            for event in response:
                if (event.event_type == "text-generation"):
                    complete_response += event.text
                elif event.event_type == "stream-end":
                    break
            return complete_response
        except Exception as e:
            raise ValueError(f"Error querying model: {e}")

    def _update_chat_history(self, question, response):
        self.chat_history.append({"role": "USER", "text": question})
        self.chat_history.append({"role": "CHATBOT", "text": response})

async def load_configuration():
    try:
        async with httpx.AsyncClient() as client:
            response = await client.get(dashboard_url)
            response.raise_for_status()
            config = response.json()
            return config
    except httpx.HTTPStatusError as e:
        raise HTTPException(status_code=500, detail=f"Configuration service error: {e.response.status_code}")
    except httpx.RequestError as e:
        raise HTTPException(status_code=500, detail=f"Configuration service request error: {e}")
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Unexpected error: {e}")

rag_pipeline = None

@app.on_event("startup")
async def startup_event():
    global rag_pipeline
    try:
        config = await load_configuration()
        print(f"Configuration loaded successfully: {config}")

        # Validate configuration keys
        required_keys = ['api_key', 'embedding_model_name', 'k', 'prompt_template']
        for key in required_keys:
            if key not in config:
                raise ValueError(f"Missing required configuration key: {key}")

        # Initialize embeddings and vector store based on configuration
        embedder = HuggingFaceInferenceAPIEmbeddings(api_key=config['api_key'], model_name=config['embedding_model_name'])
        persist_directory = 'vector_db'
        vectorstore = Chroma(persist_directory=persist_directory, embedding_function=embedder)

        # Initialize RAG pipeline
        rag_pipeline = RAGPipeline(vectorstore, config['api_key'], model_name="gemma2-9b-it", k=config['k'])
        rag_pipeline.prompt_template = PromptTemplate.from_template(config['prompt_template'])
        print("RAGPipeline initialized successfully.")
    except HTTPException as e:
        print(f"Error loading configuration: {e.detail}")
    except Exception as e:
        print(f"Unexpected error during startup: {e}")

def query(question):
    global rag_pipeline
    if rag_pipeline is None:
        raise ValueError("RAGPipeline is not initialized")
    return rag_pipeline.generate_response(question)

class QueryRequest(BaseModel):
    question: str

@app.post("/query")
async def query_endpoint(request: QueryRequest):
    try:
        response = query(request.question)
        return {"response": response}
    except Exception as e:
        return {"error": str(e)}

if __name__ == '__main__':
    import uvicorn
    uvicorn.run(app, host='0.0.0.0', port=8000)
