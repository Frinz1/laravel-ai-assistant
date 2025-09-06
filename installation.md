# 🚀 Laravel AI Assistant - Complete Installation Guide

## 📋 Table of Contents
1. [System Requirements](#system-requirements)
2. [Prerequisites Installation](#prerequisites-installation)
3. [Project Setup](#project-setup)
4. [Database Configuration](#database-configuration)
5. [Stripe Payment Integration](#stripe-payment-integration)
6. [AI Service Setup (PyTorch + FastAPI)](#ai-service-setup)
7. [Laravel Configuration](#laravel-configuration)
8. [Web Server Setup](#web-server-setup)
9. [Testing the Installation](#testing-the-installation)
10. [Production Deployment](#production-deployment)
11. [Troubleshooting](#troubleshooting)

## 🖥️ System Requirements

### Minimum Requirements
- **OS**: Windows 10/11 (64-bit)
- **RAM**: 8GB minimum, 16GB recommended
- **Storage**: 10GB free space
- **GPU**: NVIDIA GPU with CUDA support (recommended for AI service)

### Software Requirements
- **PHP**: 8.1 or higher
- **Composer**: Latest version
- **Node.js**: 18.x or higher
- **MySQL**: 8.0 or higher
- **Python**: 3.9 or higher
- **Git**: Latest version

## 🔧 Prerequisites Installation

### Step 1: Install PHP
1. Download PHP from [windows.php.net](https://windows.php.net/download/)
2. Extract to `C:\php`
3. Add `C:\php` to your system PATH
4. Copy `php.ini-development` to `php.ini`
5. Enable required extensions in `php.ini`:

\`\`\`ini
extension=curl
extension=fileinfo
extension=gd
extension=mbstring
extension=openssl
extension=pdo_mysql
extension=zip
\`\`\`

### Step 2: Install Composer
1. Download from [getcomposer.org](https://getcomposer.org/download/)
2. Run the installer
3. Verify installation:
\`\`\`cmd
composer --version
\`\`\`

### Step 3: Install Node.js
1. Download from [nodejs.org](https://nodejs.org/)
2. Install with default settings
3. Verify installation:
\`\`\`cmd
node --version
npm --version
\`\`\`

### Step 4: Install MySQL
1. Download MySQL Installer from [mysql.com](https://dev.mysql.com/downloads/installer/)
2. Choose "Developer Default" setup
3. Set root password (remember this!)
4. Start MySQL service

### Step 5: Install Python & PyTorch
1. Download Python from [python.org](https://www.python.org/downloads/)
2. **Important**: Check "Add Python to PATH" during installation
3. Verify installation:
\`\`\`cmd
python --version
pip --version
\`\`\`

4. Install PyTorch:
\`\`\`cmd
# For CUDA (if you have NVIDIA GPU)
pip install torch torchvision torchaudio --index-url https://download.pytorch.org/whl/cu118

# For CPU only
pip install torch torchvision torchaudio --index-url https://download.pytorch.org/whl/cpu
\`\`\`

### Step 6: Install Git
1. Download from [git-scm.com](https://git-scm.com/download/win)
2. Install with default settings

## 📁 Project Setup

### Step 1: Clone or Create Project
\`\`\`cmd
# If cloning from repository
git clone https://github.com/yourusername/laravel-ai-assistant.git
cd laravel-ai-assistant

# If creating new project
composer create-project laravel/laravel laravel-ai-assistant
cd laravel-ai-assistant
\`\`\`

### Step 2: Install PHP Dependencies
\`\`\`cmd
composer install
\`\`\`

### Step 3: Install Node Dependencies
\`\`\`cmd
npm install
\`\`\`

### Step 4: Environment Configuration
\`\`\`cmd
# Copy environment file
copy .env.example .env

# Generate application key
php artisan key:generate
\`\`\`

### Step 5: Update .env File
Open `.env` in your text editor and configure:

\`\`\`env
APP_NAME="AI Assistant"
APP_ENV=local
APP_KEY=base64:your-generated-key
APP_DEBUG=true
APP_URL=http://localhost:8000

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ai_assistant
DB_USERNAME=ai_assistant_user
DB_PASSWORD=your_secure_password

# Stripe Configuration (we'll set these up next)
STRIPE_KEY=pk_test_your_stripe_publishable_key
STRIPE_SECRET=sk_test_your_stripe_secret_key
STRIPE_WEBHOOK_SECRET=whsec_your_webhook_secret

# AI Service Configuration
AI_ENDPOINT=http://localhost:8001/api/chat
AI_API_KEY=your_ai_api_key
AI_TIMEOUT=30
\`\`\`

## 🗄️ Database Configuration

### Step 1: Create Database and User
Open MySQL Command Line Client or MySQL Workbench:

\`\`\`sql
-- Create database
CREATE DATABASE ai_assistant CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create user
CREATE USER 'ai_assistant_user'@'localhost' IDENTIFIED BY 'your_secure_password';

-- Grant privileges
GRANT ALL PRIVILEGES ON ai_assistant.* TO 'ai_assistant_user'@'localhost';

-- Flush privileges
FLUSH PRIVILEGES;

-- Verify
SHOW DATABASES;
SELECT User, Host FROM mysql.user WHERE User = 'ai_assistant_user';
\`\`\`

### Step 2: Run Migrations
\`\`\`cmd
php artisan migrate
\`\`\`

### Step 3: Create Admin User
\`\`\`cmd
php artisan tinker
\`\`\`

In the Tinker console:
\`\`\`php
$admin = new App\Models\User();
$admin->first_name = 'Admin';
$admin->last_name = 'User';
$admin->email = 'admin@yourdomain.com';
$admin->password = Hash::make('your_admin_password');
$admin->is_admin = true;
$admin->current_plan = 'yearly';
$admin->plan_expires_at = now()->addYear();
$admin->save();

echo "Admin user created successfully!";
exit;
\`\`\`

## 💳 Stripe Payment Integration

### Step 1: Create Stripe Account
1. Go to [stripe.com](https://stripe.com) and create an account
2. Complete account verification
3. Switch to **Test Mode** for development

### Step 2: Get API Keys
1. Go to **Developers** → **API Keys**
2. Copy your **Publishable key** (starts with `pk_test_`)
3. Copy your **Secret key** (starts with `sk_test_`)
4. Update your `.env` file:

\`\`\`env
STRIPE_KEY=pk_test_51234567890abcdef...
STRIPE_SECRET=sk_test_51234567890abcdef...
\`\`\`

### Step 3: Create Products and Prices
1. Go to **Products** in Stripe Dashboard
2. Click **Add Product**

#### Create Monthly Plan:
- **Name**: AI Assistant Pro Monthly
- **Description**: Monthly subscription to AI Assistant Pro
- **Pricing Model**: Recurring
- **Price**: $17.00 USD
- **Billing Period**: Monthly
- **Copy the Price ID** (starts with `price_`)

#### Create Yearly Plan:
- **Name**: AI Assistant Pro Yearly
- **Description**: Yearly subscription to AI Assistant Pro (2 months free)
- **Pricing Model**: Recurring
- **Price**: $100.00 USD
- **Billing Period**: Yearly
- **Copy the Price ID** (starts with `price_`)

### Step 4: Configure Webhooks
1. Go to **Developers** → **Webhooks**
2. Click **Add Endpoint**
3. **Endpoint URL**: `https://yourdomain.com/stripe/webhook` (for production)
   - For local testing: `http://localhost:8000/stripe/webhook`
4. **Events to send**:
   - `checkout.session.completed`
   - `invoice.payment_succeeded`
   - `invoice.payment_failed`
   - `customer.subscription.created`
   - `customer.subscription.updated`
   - `customer.subscription.deleted`

5. **Copy the Webhook Signing Secret** (starts with `whsec_`)
6. Update your `.env`:

\`\`\`env
STRIPE_WEBHOOK_SECRET=whsec_1234567890abcdef...
\`\`\`

### Step 5: Update Price IDs in Code
Update `app/Http/Controllers/PlanController.php`:

\`\`\`php
// Define plan prices using your actual Stripe Price IDs
$priceIds = [
    'monthly' => 'price_1234567890abcdef', // Your monthly price ID
    'yearly' => 'price_1234567890abcdef',  // Your yearly price ID
];
\`\`\`

### Step 6: Test Stripe Integration
Use these test card numbers:
- **Success**: `4242 4242 4242 4242`
- **Declined**: `4000 0000 0000 0002`
- **Requires Authentication**: `4000 0025 0000 3155`

**Test Details**:
- **Expiry**: Any future date
- **CVC**: Any 3 digits
- **ZIP**: Any 5 digits

## 🤖 AI Service Setup (PyTorch + FastAPI)

### Step 1: Create AI Service Directory
\`\`\`cmd
mkdir ai_service
cd ai_service
\`\`\`

### Step 2: Create Python Virtual Environment
\`\`\`cmd
python -m venv venv
venv\Scripts\activate
\`\`\`

### Step 3: Install Dependencies
Create `requirements.txt`:
\`\`\`txt
fastapi==0.104.1
uvicorn[standard]==0.24.0
torch>=2.0.0
transformers>=4.35.0
accelerate>=0.24.0
pydantic>=2.4.0
python-multipart>=0.0.6
openai>=1.3.0
anthropic>=0.7.0
requests>=2.31.0
python-dotenv>=1.0.0
\`\`\`

Install dependencies:
\`\`\`cmd
pip install -r requirements.txt
\`\`\`

### Step 4: Create FastAPI Application
Create `main.py`:

```python
import os
import asyncio
from typing import List, Dict, Any, Optional
from fastapi import FastAPI, HTTPException, BackgroundTasks
from pydantic import BaseModel
import torch
from transformers import AutoTokenizer, AutoModelForCausalLM
import openai
from dotenv import load_dotenv
import logging

# Load environment variables
load_dotenv()

# Configure logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

app = FastAPI(title="AI Assistant Service", version="1.0.0")

# Request/Response Models
class ChatMessage(BaseModel):
    role: str  # 'user' or 'assistant'
    content: str

class ChatRequest(BaseModel):
    messages: List[ChatMessage]
    max_tokens: Optional[int] = 2000
    temperature: Optional[float] = 0.7
    model: Optional[str] = "gpt-3.5-turbo"

class ChatResponse(BaseModel):
    response: str
    model_used: str
    tokens_used: Optional[int] = None
    response_time: float

class AIService:
    def __init__(self):
        self.openai_client = None
        self.local_model = None
        self.local_tokenizer = None
        self.device = torch.device("cuda" if torch.cuda.is_available() else "cpu")
        
        # Initialize services
        self._initialize_openai()
        self._initialize_local_model()
    
    def _initialize_openai(self):
        """Initialize OpenAI client if API key is provided"""
        api_key = os.getenv("OPENAI_API_KEY")
        if api_key:
            openai.api_key = api_key
            self.openai_client = openai
            logger.info("OpenAI client initialized")
        else:
            logger.warning("OpenAI API key not found")
    
    def _initialize_local_model(self):
        """Initialize local PyTorch model"""
        try:
            model_name = os.getenv("LOCAL_MODEL_NAME", "microsoft/DialoGPT-medium")
            logger.info(f"Loading local model: {model_name}")
            
            self.local_tokenizer = AutoTokenizer.from_pretrained(model_name)
            self.local_model = AutoModelForCausalLM.from_pretrained(model_name)
            
            # Add padding token if it doesn't exist
            if self.local_tokenizer.pad_token is None:
                self.local_tokenizer.pad_token = self.local_tokenizer.eos_token
            
            self.local_model.to(self.device)
            logger.info(f"Local model loaded on {self.device}")
            
        except Exception as e:
            logger.error(f"Failed to load local model: {e}")
            self.local_model = None
            self.local_tokenizer = None
    
    async def generate_openai_response(self, messages: List[ChatMessage], **kwargs) -> str:
        """Generate response using OpenAI API"""
        if not self.openai_client:
            raise HTTPException(status_code=503, detail="OpenAI service not available")
        
        try:
            # Convert messages to OpenAI format
            openai_messages = [{"role": msg.role, "content": msg.content} for msg in messages]
            
            response = await asyncio.to_thread(
                self.openai_client.ChatCompletion.create,
                model=kwargs.get("model", "gpt-3.5-turbo"),
                messages=openai_messages,
                max_tokens=kwargs.get("max_tokens", 2000),
                temperature=kwargs.get("temperature", 0.7)
            )
            
            return response.choices[0].message.content
            
        except Exception as e:
            logger.error(f"OpenAI API error: {e}")
            raise HTTPException(status_code=500, detail=f"OpenAI API error: {str(e)}")
    
    async def generate_local_response(self, messages: List[ChatMessage], **kwargs) -> str:
        """Generate response using local PyTorch model"""
        if not self.local_model or not self.local_tokenizer:
            raise HTTPException(status_code=503, detail="Local model not available")
        
        try:
            # Get the last user message
            user_message = messages[-1].content if messages else ""
            
            # Tokenize input
            inputs = self.local_tokenizer.encode(
                user_message + self.local_tokenizer.eos_token, 
                return_tensors="pt"
            ).to(self.device)
            
            # Generate response
            with torch.no_grad():
                outputs = self.local_model.generate(
                    inputs,
                    max_length=inputs.shape[1] + kwargs.get("max_tokens", 200),
                    temperature=kwargs.get("temperature", 0.7),
                    do_sample=True,
                    pad_token_id=self.local_tokenizer.eos_token_id,
                    attention_mask=torch.ones_like(inputs)
                )
            
            # Decode response
            response = self.local_tokenizer.decode(
                outputs[0][inputs.shape[1]:], 
                skip_special_tokens=True
            )
            
            return response.strip() if response.strip() else "I'm sorry, I couldn't generate a proper response."
            
        except Exception as e:
            logger.error(f"Local model error: {e}")
            raise HTTPException(status_code=500, detail=f"Local model error: {str(e)}")
    
    async def generate_fallback_response(self, messages: List[ChatMessage]) -> str:
        """Generate a fallback response when other services fail"""
        user_message = messages[-1].content if messages else ""
        
        fallback_responses = [
            f"I understand you're asking about: '{user_message}'. I'm currently experiencing some technical difficulties, but I'm here to help. Could you please try rephrasing your question?",
            "Thank you for your message. I'm having trouble connecting to my AI processing system right now. Please try again in a moment, and I'll do my best to provide a helpful response.",
            f"I received your message about '{user_message}'. While I'm working on resolving some technical issues, I want to ensure I give you the best possible answer. Could you provide a bit more context about what you're looking for?",
        ]
        
        import random
        return random.choice(fallback_responses)

# Initialize AI service
ai_service = AIService()

@app.get("/")
async def root():
    return {"message": "AI Assistant Service is running", "status": "healthy"}

@app.get("/health")
async def health_check():
    """Health check endpoint"""
    status = {
        "status": "healthy",
        "services": {
            "openai": ai_service.openai_client is not None,
            "local_model": ai_service.local_model is not None,
            "device": str(ai_service.device)
        }
    }
    return status

@app.post("/api/chat", response_model=ChatResponse)
async def chat(request: ChatRequest):
    """Main chat endpoint"""
    import time
    start_time = time.time()
    
    try:
        # Try OpenAI first if available
        if ai_service.openai_client and request.model.startswith("gpt"):
            response_text = await ai_service.generate_openai_response(
                request.messages,
                model=request.model,
                max_tokens=request.max_tokens,
                temperature=request.temperature
            )
            model_used = request.model
            
        # Try local model
        elif ai_service.local_model:
            response_text = await ai_service.generate_local_response(
                request.messages,
                max_tokens=request.max_tokens,
                temperature=request.temperature
            )
            model_used = "local_pytorch_model"
            
        # Fallback response
        else:
            response_text = await ai_service.generate_fallback_response(request.messages)
            model_used = "fallback"
        
        response_time = time.time() - start_time
        
        return ChatResponse(
            response=response_text,
            model_used=model_used,
            response_time=response_time
        )
        
    except Exception as e:
        logger.error(f"Chat error: {e}")
        # Return fallback response on any error
        response_text = await ai_service.generate_fallback_response(request.messages)
        response_time = time.time() - start_time
        
        return ChatResponse(
            response=response_text,
            model_used="fallback",
            response_time=response_time
        )

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(
        "main:app",
        host="0.0.0.0",
        port=8001,
        reload=True,
        log_level="info"
    )
