from fastapi import FastAPI, Request
from pydantic import BaseModel
from dotenv import load_dotenv
import os
from langchain_community.document_loaders import BSHTMLLoader
from langchain_text_splitters import RecursiveCharacterTextSplitter
from langchain_community.embeddings import HuggingFaceInferenceAPIEmbeddings
from langchain_chroma import Chroma
from langchain_core.prompts import PromptTemplate
from groq import Groq

load_dotenv(dotenv_path='.env.langchain')

hugging_api_key = os.getenv('HUGGING_FACE_API_KEY')
groq_api_key = os.getenv('GROQ_API_KEY')

file_path = "./AkrabElikApp.html"

loader = BSHTMLLoader(file_path, open_encoding="utf-8")
docs = loader.load()

text_splitter = RecursiveCharacterTextSplitter(chunk_size=1000, chunk_overlap=200)
splits = text_splitter.split_documents(docs)

embedder = HuggingFaceInferenceAPIEmbeddings(
    api_key=hugging_api_key, model_name="Griffin88/sentence-embedding-LaBSE"
)

vectorstore = Chroma.from_documents(documents=splits, embedding=embedder)

class RAGPipeline:
    def __init__(self, vectorstore, api_key, model_name="gemma2-9b-it", k=6):
        self.vectorstore = vectorstore
        self.api_key = api_key
        self.model_name = model_name
        self.k = k
        self.retriever = self.vectorstore.as_retriever(search_type="similarity", search_kwargs={"k": self.k})
        self.prompt_template = PromptTemplate.from_template(self._get_template())
        self.client = Groq(api_key=self.api_key)

    def _get_template(self):
        return """
        استخدم السياقات التالية للإجابة على السؤال في النهاية.
        إذا كنت لا تعرف الإجابة، فقط قل إنك لا تعرف، لا تحاول تصنيع إجابة.
        حافظ على إجابتك شاملة وصحيحة قدر الإمكان.
        دائمًا قل 'شكرًا للسؤال!' في نهاية الإجابة.
        \n السياق: {context}
        \n السؤال: {question}
        \n الإجابة المفيدة:
        """

    def generate_response(self, question):
        retrieved_docs = self._retrieve_documents(question)
        prompt = self._create_prompt(retrieved_docs, question)
        response = self._query_model(prompt)
        return response

    def _retrieve_documents(self, question):
        retrieved_docs = self.retriever.invoke(question)
        return {f'doc_{i}': doc.page_content for i, doc in enumerate(retrieved_docs)}

    def _create_prompt(self, docs, question):
        return self.prompt_template.format(context=docs, question=question)

    def _query_model(self, prompt):
        completion = self.client.chat.completions.create(
            model=self.model_name,
            messages=[{"role": "user", "content": prompt}],
            temperature=1,
            max_tokens=1024,
            top_p=1,
            stream=True,
            stop=None,
        )

        response = ""
        for chunk in completion:
            response += chunk.choices[0].delta.content or ""

        return response

rag_pipeline = RAGPipeline(vectorstore, groq_api_key)

def query(question):
    return rag_pipeline.generate_response(question)

app = FastAPI()

class QueryRequest(BaseModel):
    question: str

@app.post("/query")
async def query_endpoint(request: QueryRequest):
    response = query(request.question)
    return {"response": response}

if __name__ == '__main__':
    import uvicorn
    uvicorn.run(app, host='0.0.0.0', port=8000)
