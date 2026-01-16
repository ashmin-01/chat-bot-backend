


https://github.com/user-attachments/assets/3aa2c077-5696-4140-b226-82fe2d758f1b


# 🤖 Syriatel AI-Powered Customer Support Chatbot

## 📌 Project Overview
The **Syriatel AI-Powered Customer Support Chatbot** is a backend-focused system designed to automate and enhance customer support using **AI and NLP**.  
The platform provides contextual responses to user queries, manages content and feedback, and includes a secure **admin dashboard** for managing users and content.

The system combines **FastAPI** backend services with **AI/NLP pipelines** using LangChain and RAG, delivering a scalable, responsive, and intelligent support system.

---

## 🚀 Features

### 🤖 Chatbot / AI Features
- Contextual responses powered by AI and NLP
- Handles user queries in multiple languages
- Supports content-based responses using **RAG** (Retrieval-Augmented Generation)
- Feedback collection for continuous improvement

### 🛠️ Backend Features
- CRUD APIs for managing user queries, content, and feedback
- Secure endpoints for admin and user operations
- Fast and scalable backend built with **FastAPI**

### 🖥️ Admin Dashboard
- Manage users and their queries
- Manage and update content for AI responses
- Oversee feedback and performance analytics
- Secure API access for admin operations

---

## 🏗️ System Architecture
- **Backend:** Laravel, FastAPI (Python)  
- **AI/NLP:** LangChain, RAG pipelines  
- **Database:** MySQL  
- **Web Admin Dashboard:** Laravel  
- **API Architecture:** RESTful APIs  

The backend serves as the core, integrating AI pipelines to generate responses, store and retrieve content, and handle user interactions securely.

---

## 🛠️ Tech Stack
- **Backend:** FastAPI (Python), Laravel
- **AI / NLP:** LangChain, RAG  
- **Database:** MySQL  
- **API:** RESTful APIs  
- **Other Tools:** Docker (optional), Git

---

## ⚙️ Installation & Setup

### Backend Setup
```bash
git clone https://github.com/ashmin-01/chat-bot-backend.git
cd chat-bot-backend
pip install -r requirements.txt
cp .env.example .env
# configure database and AI API keys in .env
uvicorn main:app --reload
