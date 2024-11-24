import Groq from "groq-sdk";
import dotenv from 'dotenv';

dotenv.config();

const groq = new Groq({ apiKey: process.env.ACCESS_TOKEN_SECRET });

export async function main() {
  const chatCompletion = await getGroqChatCompletion();
  console.log(chatCompletion);
}

export async function getGroqChatCompletion(prompt) {
  return groq.chat.completions.create({
    messages: [
      {
        role: "user",
        content: prompt,
      },
    ],
    model: "gemma2-9b-it",
  });
}