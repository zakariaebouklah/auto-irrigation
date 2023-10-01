FROM python:3.10.2

WORKDIR /py-app

COPY ./requirements.txt ./
COPY ./main.py ./
COPY ./calculation ./calculation

RUN pip3 install -r requirements.txt

EXPOSE 8080

CMD [ "uvicorn", "main:app", "--proxy-headers", "--host", "0.0.0.0", "--port", "8080", "--reload" ]