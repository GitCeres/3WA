// Compte à rebours
let countdown = 600;

// Erreurs
const LETTERS = "La réponse doit contenir que des lettres";
const FALSE = "La réponse est fausse";
const NUMBERS = "La réponse doit contenir que des chiffres";

// Premier form
const FIRST = "blanc";
const FIRST_SOLUTION = document.getElementById('firstSolutionEscape');

// Deuxière form
const SECOND = 8;
const SECOND_SOLUTION = document.getElementById('secondSolutionEscape');

// Troisème form
const THIRD = 1492;
const THIRD_SOLUTION = document.getElementById('thirdSolutionEscape');

// Regex
const REGEX_STRINGS = /^[a-z]+$/;
const REGEX_NUMBERS = /^[0-9]+$/;

// Éléments du DOM
const COUNTDOWN_ESCAPE = document.getElementById("countdownEscape");
const WIN_REPLAY = document.getElementById('win-replay-escape');
const LOOSE_REPLAY = document.getElementById('loose-replay-escape');
const CONGRATS = document.getElementById('congrats');
const LOOSE = document.getElementById('loose');
const NEXT = document.getElementById('next');
const FIRST_FORM_ESCAPE = document.forms['firstFormEscape'];
const SECOND_FORM_ESCAPE = document.forms['secondFormEscape'];
const THIRD_FORM_ESCAPE = document.forms['thirdFormEscape'];

let firstGood = false;
let secondGood = false;

// Récupère les données des formulaires
function getData(e) {
    e.preventDefault();

    if (FIRST_FORM_ESCAPE) {
        let firstContent = document.firstFormEscape.firstInputEscape.value;
        let secondContent = document.secondFormEscape.secondInputEscape.value;

        if (firstContent && !firstGood) {
            testForm(firstContent, 'first');
        } else if (secondContent && !secondGood) {
            testForm(secondContent, 'second');
        }

    } else if (THIRD_FORM_ESCAPE) {
        let thirdContent = document.thirdFormEscape.thirdInputEscape.value

        if (thirdContent) {
            testForm(thirdContent, 'third');
        }
    }
}

// Vérifie si la réponse est bonne
function testForm(data, form) {
    if (form === 'first') {
        let contentLower = data.toLowerCase();

        if (!REGEX_STRINGS.test(contentLower)) {
            errorForm(LETTERS, form);
            return;
        }

        if (contentLower != FIRST) {
            errorForm(FALSE, form);
            return;
        }

        firstGood = true;
        validForm(form);

    } else if (form === 'second') {
        if (!REGEX_NUMBERS.test(data)) {
            errorForm(NUMBERS, form);
            return;
        }

        if (data != SECOND) {
            errorForm(FALSE, form);
            return;
        }

        secondGood = true;
        validForm(form);
    } else if (form === 'third') {
        if (!REGEX_NUMBERS.test(data)) {
            errorForm(NUMBERS, form);
            return;
        }

        if (data != THIRD) {
            errorForm(FALSE, form);
            return;
        }

        validForm(form);
    }
}

// Modifie l'affichage lors d'une bonne réponse
function validForm(form) {
    let inputEscape = document.getElementById(form + "InputEscape");
    let testSubmitEscape = document.getElementById(form + "TestSubmitEscape");
    let formSendEscape = document.getElementById(form + "FormSendEscape");
    let successSubmitEscape = document.getElementById(form + "SuccessSubmitEscape");
    let divGoodSubmit = document.getElementById(form + "DivGoodSubmit");

    inputEscape.disabled = true;
    testSubmitEscape.classList.toggle("d-none");
    formSendEscape.classList.remove("btn-primary");
    formSendEscape.classList.add("btn-success");
    successSubmitEscape.classList.toggle("d-none");
    divGoodSubmit.classList.toggle("d-none");

    if (form === 'third') {
        endGame();
        return;
    }

    testAllGood();
}

// Modifie l'affichage lors d'une erreur
function errorForm(error, form) {
    let inputEscape = document.getElementById(form + "InputEscape");
    let formSendEscape = document.getElementById(form + "FormSendEscape");
    let testSubmitEscape = document.getElementById(form + "TestSubmitEscape");
    let failSubmitEscape = document.getElementById(form + "FailSubmitEscape");
    let divFailSubmit = document.getElementById(form + "DivFailSubmit");
    let paraFailSubmit = document.getElementById(form + "ParaFailSubmit");

    inputEscape.disabled = true;
    formSendEscape.classList.remove("btn-primary");
    formSendEscape.classList.add("btn-danger");
    testSubmitEscape.classList.toggle("d-none");
    failSubmitEscape.classList.toggle("d-none");
    divFailSubmit.classList.toggle("d-none");
    paraFailSubmit.innerHTML = error;

    setTimeout(function () {
        failSubmitEscape.classList.toggle("d-none");
        testSubmitEscape.classList.toggle("d-none");
        formSendEscape.classList.remove("btn-danger");
        formSendEscape.classList.add("btn-primary");
        divFailSubmit.classList.toggle("d-none");
        inputEscape.value = "";
        inputEscape.disabled = false;
        inputEscape.focus();
    }, 2e3)
}

// Vérifie que tous les forms de la page 1 ont étés résoluts
function testAllGood() {
    if (!firstGood || !secondGood) {
        return;
    }
    
    clearInterval(countdownTimer);

    document.cookie = "escape-p1=win; max-age=3600";
    document.cookie = "countdown=" + countdown + "; max-age=3600";
    NEXT.classList.toggle("d-none");
}

// Partie gagnée
function endGame() {
    clearInterval(countdownTimer);

    ended = convertTimeToNumber(COUNTDOWN_ESCAPE.textContent);

    deleteCookies();
    document.cookie = "ended=" + ended + "; max-age=3600";
    CONGRATS.classList.toggle("d-none");
}

// Partie perdue
function looseGame() {
    const forms = ["first", "second", "third"];

    clearInterval(countdownTimer);

    LOOSE.classList.toggle("d-none");

    for (const form of forms) {
        let formInputEscape = document.getElementById(form + "InputEscape");
        if (formInputEscape) {
            formInputEscape.disabled = true;
        }
    }
}

// Relance une partie
function replay() {
    deleteCookies();
    location.reload();
}

// Affiche la solution de l'énigme
function solution(number) {
    if (number === "first") {
        alert('La réponse est "blanc"');
    } else if (number === "second") {
        alert('La réponse est "8"');
    } else if (number === "third") {
        alert('La réponse est "1492"');
    }
}

// Permet de savoir si un cookie existe
function getCookie(cname) {
    let name = cname + "=";
    let decodedCookie = decodeURIComponent(document.cookie);
    let ca = decodedCookie.split(';');
    for(let i = 0; i <ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return false;
}

// Supprime les cookies
function deleteCookies() {
    document.cookie = "ended=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/escape_game;";
    document.cookie = "countdown=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/escape_game;";
    document.cookie = "countdown=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    document.cookie = "escape-p1=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
}

// Convertie le temps en minutes et secondes
function convertNumberToTime(countdown) {
    let minutes = parseInt(countdown / 60, 10);
    let seconds = parseInt(countdown % 60, 10);

    if (minutes < 10) {
        minutes = "0" + minutes;
    }

    if (seconds < 10) {
        seconds = "0" + seconds;
    }

    return {minutes, seconds};
}

function convertTimeToNumber(countdown) {
    let time = countdown.split(":");
    let minutes = parseInt(time[0] * 60, 10);
    let seconds = parseInt(time[1] % 60, 10);

    return minutes + seconds;
}

// Gère le temps du compte à rebours
let cookiesCountdown = parseInt(getCookie("countdown"));
let cookiesEnded = parseInt(getCookie("ended"));

if (cookiesCountdown) {
    countdown = cookiesCountdown;
    document.cookie = "countdown=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
}

let countdownTimer = setInterval(function() {
    if(cookiesEnded) {
        countdown = cookiesEnded;

        let {minutes, seconds} = convertNumberToTime(countdown);
    
        COUNTDOWN_ESCAPE.innerHTML = minutes + ":" + seconds;

        validForm('third');
    } else if (cookiesCountdown === 0) {
        COUNTDOWN_ESCAPE.innerHTML = "00:00";

        looseGame();
    } else {
        let {minutes, seconds} = convertNumberToTime(countdown);
    
        COUNTDOWN_ESCAPE.innerHTML = minutes + ":" + seconds;
        document.cookie = "countdown=" + countdown + "; max-age=3600";
        
        if (countdown <= 0) {
            looseGame()
        }
    
        countdown--;
    }
}, 1000)

LOOSE_REPLAY.addEventListener("click", replay.bind());

if (FIRST_FORM_ESCAPE) {
    FIRST_FORM_ESCAPE.addEventListener("submit", getData);
    FIRST_SOLUTION.addEventListener("click", solution.bind(null, "first"));
    SECOND_FORM_ESCAPE.addEventListener("submit", getData);
    SECOND_SOLUTION.addEventListener("click", solution.bind(null, "second"));
} else if (THIRD_FORM_ESCAPE) {
    document.body.style.background = "url(/images/escape-suite.jpg) no-repeat center";
    document.body.style.backgroundSize = "cover";
    THIRD_FORM_ESCAPE.addEventListener("submit", getData);
    THIRD_SOLUTION.addEventListener("click", solution.bind(null, "third"));
    WIN_REPLAY.addEventListener("click", replay.bind());
}