<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern Chatbot</title>
    <style>
        /* General Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #333;
        }

        /* Chat Container */
        #chat-container {
            width: 360px;
            max-width: 90vw;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 80vh;
        }

        /* Messages Container */
        #messages {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        #messages div {
            max-width: 80%;
            padding: 10px 15px;
            border-radius: 15px;
            line-height: 1.4;
            word-wrap: break-word;
            white-space: pre-wrap;
        }
        /* User and Bot Message Styles */
        #messages .user {
            align-self: flex-end;
            background-color: #e1f5fe;
            color: #0078d4;
            border-radius: 15px 15px 0 15px;
        }
        #messages .bot {
            align-self: flex-start;
            background-color: #f1f1f1;
            color: #333;
            border-radius: 15px 15px 15px 0;
        }

        /* Input Container */
        #input-container {
            display: flex;
            padding: 10px;
            background: #fafafa;
            border-top: 1px solid #ddd;
        }

        /* Input Field */
        #input {
            flex: 1;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            outline: none;
            transition: border-color 0.3s;
        }
        #input:focus {
            border-color: #0078d4;
        }

        /* Send Button */
        #send-btn {
            background: linear-gradient(135deg, #0078d4, #764ba2);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 10px 16px;
            margin-left: 8px;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.3s, transform 0.2s;
        }
        #send-btn:hover {
            background: linear-gradient(135deg, #005bb5, #5d3b82);
        }
        #send-btn:active {
            transform: scale(0.95);
        }
    </style>
</head>
<body>

<div id="chat-container">
    <div id="messages"></div>
    <div id="input-container">
        <input type="text" id="input" placeholder="Type your message here..." />
        <button id="send-btn">Send</button>
    </div>
</div>

<script>
    const API_KEY = "Your gemini api key";
    const API_URL = `https://generativelanguage.googleapis.com/v1/models/gemini-pro:generateContent?key=${API_KEY}`;

    const messagesDiv = document.getElementById('messages');
    const inputField = document.getElementById('input');
    const sendButton = document.getElementById('send-btn');

    function appendMessage(text, isUser) {
        const message = document.createElement('div');
        message.textContent = text;
        message.classList.add(isUser ? 'user' : 'bot');
        messagesDiv.appendChild(message);
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
    }

    async function generateResponse(userMessage) {
        const prompt = `Enter your custom prompt:${userMessage}`;

        const response = await fetch(API_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                contents: [{
                    role: "user",
                    parts: [{ text: prompt }]
                }]
            })
        });

        if (!response.ok) {
            throw new Error('Failed to get response from Gemini API');
        }

        const data = await response.json();
        return data.candidates[0].content.parts[0].text;
    }

    sendButton.addEventListener('click', async () => {
        const userMessage = inputField.value;
        if (!userMessage) return;

        appendMessage(userMessage, true);
        inputField.value = '';

        appendMessage("Thinking...", false);
        try {
            const botResponse = await generateResponse(userMessage);
            messagesDiv.lastChild.remove(); // Remove "Thinking..." message
            appendMessage(botResponse, false);
        } catch (error) {
            messagesDiv.lastChild.remove(); // Remove "Thinking..." message
            appendMessage(`Error: ${error.message}`, false);
        }
    });

    inputField.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            sendButton.click();
        }
    });
</script>

</body>
</html>