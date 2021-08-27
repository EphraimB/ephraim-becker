from flask import Flask, render_template
from datetime import date, datetime

app = Flask(__name__)

@app.route('/')
def index():
    born = datetime(1996, 7, 19)
    today = date.today()
    age = today.year - born.year - ((today.month, today.day) < (born.month, born.day))
    return render_template('index.html', age=age)
