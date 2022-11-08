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
    document.getElementById(form + "InputEscape").disabled = true;
    document.getElementById(form + "TestSubmitEscape").classList.toggle("d-none");
    document.getElementById(form + "FormSendEscape").classList.remove("btn-primary");
    document.getElementById(form + "FormSendEscape").classList.add("btn-success");
    document.getElementById(form + "SuccessSubmitEscape").classList.toggle("d-none");
    document.getElementById(form + "DivGoodSubmit").classList.toggle("d-none");

    if (form === 'third') {
        return;
    }

    testAllGood();
}

// Modifie l'affichage lors d'une erreur
function errorForm(error, form) {
    document.getElementById(form + "InputEscape").disabled = true;
    document.getElementById(form + "FormSendEscape").classList.remove("btn-primary");
    document.getElementById(form + "FormSendEscape").classList.add("btn-danger");
    document.getElementById(form + "TestSubmitEscape").classList.toggle("d-none");
    document.getElementById(form + "FailSubmitEscape").classList.toggle("d-none");
    document.getElementById(form + "DivFailSubmit").classList.toggle("d-none");
    document.getElementById(form + "ParaFailSubmit").innerHTML = error;

    setTimeout(function () {
        document.getElementById(form + "FailSubmitEscape").classList.toggle("d-none");
        document.getElementById(form + "TestSubmitEscape").classList.toggle("d-none");
        document.getElementById(form + "FormSendEscape").classList.remove("btn-danger");
        document.getElementById(form + "FormSendEscape").classList.add("btn-primary");
        document.getElementById(form + "DivFailSubmit").classList.toggle("d-none");
        document.getElementById(form + "InputEscape").value = "";
        document.getElementById(form + "InputEscape").disabled = false;
        document.getElementById(form + "InputEscape").focus();
    }, 2e3)
}

// Vérifie que tous les forms de la page 1 ont étés résoluts
function testAllGood() {
    if (!firstGood || !secondGood) {
        return;
    }

    document.cookie = "escape-p1=win; max-age=3600";
    NEXT.classList.toggle("d-none");
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
}