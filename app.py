from flask import Flask, render_template
from datetime import date, datetime

app = Flask(__name__)

@app.route('/')
def index():
    born = datetime(1996, 7, 19)
    today = date.today()
    age = today.year - born.year - ((today.month, today.day) < (born.month, born.day))
    return render_template('index.html', age=age)

@app.route('/timeline/')
def timeline():
    return render_template('timeline.html')

@app.route('/everydayLife/')
def everydayLife():
    return render_template('everydayLife.html')

@app.route('/everydayLife/problems/')
def problems():
    return render_template('problems.html')

@app.route('/everydayLife/accomplishments/')
def accomplishments():
    return render_template('accomplishments.html')

@app.route('/everydayLife/accomplishments/gamingSetup/')
def gamingSetup():
    return render_template('gamingSetup.html')

@app.route('/everydayLife/goals/')
def goals():
    return render_template('goals.html')

@app.route('/college/')
def college():
    return render_template('college.html')

@app.route('/projects/')
def projects():
    return render_template('projects.html')

@app.route('/resources/')
def resources():
    return render_template('resources.html')

@app.route('/about/')
def about():
    return render_template('about.html')
