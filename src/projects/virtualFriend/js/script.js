var submitButton = document.getElementById("submitButton");
var personOneTalk = document.getElementById("personOneTalk");
var personTwoSays = document.getElementById("personTwoSays");
var personTwoTalk = document.getElementById("personTwoTalk");
var personOne = document.getElementById("personOne");
var personTwo = document.getElementById("personTwo");
var personTwoFace = document.getElementById("personTwoFace");
var personTwoBody = document.getElementById("personTwoBody");
var personTwoLeftArm = document.getElementById("personTwoLeftArm");
var personTwoRightArm = document.getElementById("personTwoRightArm");
var personTwoLeftLeg = document.getElementById("personTwoLeftLeg");
var personTwoRightLeg = document.getElementById("personTwoRightLeg");
var clock = document.getElementById("clock");
var sky = document.getElementById("sky");
var ground = document.getElementById("ground");
var star = document.getElementById("star");
var accountMenu = document.getElementById("accountMenu");
var account = document.getElementById("account");
var registrationForm = document.getElementById("registrationForm");
var closeRegistrationForm = document.getElementById("closeRegistrationForm");
var register = document.getElementById("register");
var logInForm = document.getElementById("logInForm");
var closeLogInForm = document.getElementById("closeLogInForm");
var logIn = document.getElementById("logIn");
var avatarInput = document.getElementById("avatarInput");
var avatar = document.getElementById("avatar");

var submitButtonTwo = document.createElement("div");
submitButtonTwo.setAttribute("class", "submitButton");

var submitButtonThree = document.createElement("div");
submitButtonThree.setAttribute("class", "submitButton");

var submitButtonFour = document.createElement("div");
submitButtonFour.setAttribute("class", "submitButton");

var submitButtonFive = document.createElement("div");
submitButtonFive.setAttribute("class", "submitButton");

var conversationPart;

if(localStorage.firstName == undefined) {
  conversationPart = 0;
}
else if(localStorage.firstName != undefined) {
  conversationPart = 1;
}

var space = "";

var virtualFriend = {
                        talk: function(text)
                              {
                                  return personOneTalk.innerHTML = text;
                              },

                        age: localStorage.virtualFriendAge,

                        mood: "",

                        personality: localStorage.virtualFriendPersonality,

                        interests: localStorage.virtualFriendInterests,

                        flexible: localStorage.virtualFriendFlexible,

                        disorder: localStorage.virtualFriendDisorder

                    };

var you = {
              clearTextField: function()
                              {
                                  return personTwoSays.value = "";
                              },

              says: function()
                    {
                        return personTwoSays.value;
                    },

              firstName: localStorage.firstName,

              middleName: localStorage.middleName,

              lastName: localStorage.lastName,

              fullName: localStorage.fullName,

              age: localStorage.age,

              mood: "",

              personality: localStorage.personality,

              interests: localStorage.interests,

              flexible: localStorage.flexible,

              disorder: localStorage.disorder

          };

var threeDimension = document.getElementById("threeDimension");

var greeting;

function updateClock()
{
    var currentTime = new Date();
    var currentHours = currentTime.getHours();
    var currentMinutes = currentTime.getMinutes();
    var AMOrPM;

    if(currentHours < 12)
    {
        AMOrPM = "AM";
    }

    else if(currentHours >= 12)
    {
        AMOrPM = "PM";
    }

    else
    {
        AMOrPM = "";
    }

    var currentHoursTwelveHourMode;

    if((currentHours >= 13) && (currentHours < 24))
    {
        currentHoursTwelveHourMode = currentHours - 12;
    }

    else if(currentHours == 0)
    {
        currentHoursTwelveHourMode = currentHours + 12;
    }

    else
    {
        currentHoursTwelveHourMode = currentHours;
    }

    var currentMinutesCorrectlyFormatted;

    if((currentMinutes >= 0) && (currentMinutes < 10))
    {
        currentMinutesCorrectlyFormatted = "0" + currentMinutes;
    }

    else
    {
        currentMinutesCorrectlyFormatted = currentMinutes;
    }

    clock.innerHTML = currentHoursTwelveHourMode + ":" + currentMinutesCorrectlyFormatted + " " + AMOrPM;

    var skyColor;
    var groundColor;
    var starType;
    var starShadow;

    if((currentHours >= 6) && (currentHours < 12))
    {
        skyColor = "rgb(0, 0, 255)";
        groundColor = "rgb(0, 255, 0)";
        starType = "yellow";
        starShadow = "yellow";
    }

    else if((currentHours == 5) && (currentMinutes >= 50))
    {
        skyColor = "pink";
        groundColor = "rgb(0, 255, 0)";
        starType = "light yellow";
    }

    else if((currentHours == 17) && (currentMinutes >= 50))
    {
        skyColor = "pink";
        groundColor = "rgb(0, 255, 0)";
        starType = "rgb(175, 0, 0)";
    }

    else if((currentHours >= 12) && (currentHours <= 17))
    {
        skyColor = "rgb(0, 0, 200)";
        groundColor = "rgb(0, 127, 0)";
        starType = "yellow";
        starShadow = "yellow";
    }

    else if((currentHours >= 18) && (currentHours < 24))
    {
        skyColor = "rgb(0, 0, 100)";
        groundColor = "rgb(0, 75, 0)";
        starType = "white";
        starShadow = "white";
    }

    else if((currentHours >= 0) && (currentHours <= 5))
    {
        skyColor = "rgb(0, 0, 100)";
        groundColor = "rgb(0, 75, 0)";
        starType = "white";
        starShadow = "white";
    }

    else
    {
        skyColor = "";
        groundColor = "";
        starType = "";
        starShadow = "";
    }

    sky.style.background = skyColor;
    ground.style.background = groundColor;
    star.style.background = starType;
    star.style.boxShadow = "0px 0px 20px 5px " + starShadow;

    if((currentHours >= 0) && (currentHours < 12))
    {
        greeting = "Good morning";
    }

    else if((currentHours >= 12) && (currentHours < 18))
    {
        greeting = "Good afternoon";
    }

    else if((currentHours >= 18) && (currentHours < 24))
    {
        greeting = "Good evening";
    }

    else
    {
        greeting = "Hello";
    }

};

updateClock();

setInterval(updateClock, 500);

function wordInFullSentance(word, fullSentance)
{
    return fullSentance.split(" ").some(function(findTheWord)
    {
        return findTheWord == word;
    });

};

function letterInFullSentance(letter, fullSentance)
{
    return fullSentance.split("").some(function(findTheLetter)
    {
        return findTheLetter == letter;
    });

};

function capitalize(word)
{
    return word.charAt(0).toUpperCase() + word.slice(1);
};

function nameSort(sentance)
{
    var start;

    if(wordInFullSentance("is", sentance))
    {
        start = sentance.search("is");

        localStorage.fullName = sentance.slice(start + 3);
    }

    else
    {
        localStorage.fullName = sentance;
    }

    if(letterInFullSentance(" ", localStorage.fullName))
    {
        start = localStorage.fullName.search(" ");
        space = " ";

        localStorage.firstName = capitalize(localStorage.fullName.slice(0, start));
        localStorage.lastName = capitalize(localStorage.fullName.slice(start + 1));
    }

    else
    {
        localStorage.firstName = capitalize(localStorage.fullName);
    }

    if(localStorage.lastName != undefined) {
      localStorage.fullName = localStorage.firstName + space + localStorage.lastName;
    }
    else if(localStorage.lastName == undefined) {
      localStorage.fullName = localStorage.firstName
    }

    you.firstName = localStorage.firstName;
    you.lastName = localStorage.lastName;
    you.fullName = localStorage.fullName;
};

function ageSort(sentance) {
  var start;

  if(wordInFullSentance("is", sentance))
  {
      start = sentance.search("is");

      localStorage.age = sentance.slice(start + 3);
  }

  else
  {
      localStorage.age = sentance;
  }

  you.age = localStorage.age;
}

function conversation() {
  if(conversationPart == 0) {
    virtualFriend.talk(greeting + "! What's your name?");

    submitButton.onclick = function()
    {
      conversationPart = 1;
      conversation();
    }
  }

  if(conversationPart == 1) {
    submitButton.style.display = "none";
    submitButtonTwo.innerHTML = "Submit";
    personTwoTalk.appendChild(submitButtonTwo);
    submitButtonTwo.style.display == "block";

    if(localStorage.firstName == undefined) {
      nameSort(you.says());
    }

    virtualFriend.talk("Hi, " + you.firstName + "! How are you doing?");
    you.clearTextField();

    submitButtonTwo.onclick = function() {
      conversationPart = 2;
      conversation();
    }
  }

  if(conversationPart == 2) {
    submitButtonTwo.style.display = "none";
    submitButtonThree.innerHTML = "Submit";
    personTwoTalk.appendChild(submitButtonThree);
    submitButtonThree.style.display == "block";

    if(you.says() == "Good") {
      virtualFriend.talk("Nice to hear! How old are you?");
    }

    if(you.says() == "Bad") {
      virtualFriend.talk("Sorry to hear that!");
    }

    you.clearTextField();

    submitButtonThree.onclick = function() {
      conversationPart = 3;
      conversation();
    }
  }

  if(conversationPart == 3) {
    submitButtonThree.style.display = "none";
    submitButtonFour.innerHTML = "Submit";
    personTwoTalk.appendChild(submitButtonFour);
    submitButtonThree.style.display == "block";

    if(localStorage.age == undefined) {
      ageSort(you.says());
    }

    if(localStorage.age < "13") {
      virtualFriend.talk("Still a child.");
    }

    else if(localStorage.age >= "13" && localStorage.age <= "18") {
      virtualFriend.talk("Wow! You're a teenager.");
    }

    else if(localStorage.age >= "18" && localStorage.age <= "25") {
      virtualFriend.talk("Already an adult. You're young still.");
    }

    else if(localStorage.age > "25" && localStorage.age <= "50") {
      virtualFriend.talk("Already an adult.");
    }

    else if(localStorage.age > "50") {
      virtualFriend.talk("You're getting old.");
    }

    you.clearTextField();

    submitButtonFour.onclick = function()
    {
      conversationPart = 4;
      conversation();
    }
  }

  if(conversationPart == 4) {
    submitButtonFour.style.display = "none";
    submitButtonFive.innerHTML = "Submit";
    personTwoTalk.appendChild(submitButtonFive);
    submitButtonFour.style.display == "block";


    you.clearTextField();
  }
}

conversation();
