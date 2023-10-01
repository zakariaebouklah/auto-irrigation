from fastapi import FastAPI, Request
from fastapi.middleware.cors import CORSMiddleware
from calculation import calculation_script
import json

app = FastAPI()

"""Enabling CORS."""

origins = ['https://localhost:8000']

app.add_middleware(
    CORSMiddleware,
    allow_origins=origins,
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"]
)

"""Endpoints"""

@app.get("/")
def root():
    return {"message" : "Hello Zaki!!!"}

@app.post("/fastapi/make/calculations")
async def make_calculations(req: Request):

    json_data = await req.json()

    return calculation_script.calculation(json_data=json_data)

"""
    TODO: i need 1 crons : get (jr-1) data from the database then store calculations.
"""