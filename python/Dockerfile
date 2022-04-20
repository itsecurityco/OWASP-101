FROM python:3
ENV PYTHONDONTWRITEBYTECODE=1
ENV PYTHONUNBUFFERED=1
WORKDIR /code
COPY requirements.txt /code/
RUN pip install -r requirements.txt
COPY . /code/
# RUN python manage.py makemigrations bank
# RUN python manage.py migrate
# RUN python manage.py loaddata bank/fixtures/*