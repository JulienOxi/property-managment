import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';
import './styles/filemanager.css';

//fonction de validation des formulaire
// Cette fonction prend l'ID du champ d'entrée (inputId) et une expression régulière (regex) comme paramètres.
//exemple :   validateInput("_postal_code", '/^\d{5}$/');
/**
 * Fonction pour valider un champ d'entrée basé sur une expression régulière
 * @param {*} inputId 
 * @param {*} regex 
 * @param {*} capitalize 
 */
window.validateInput = function(inputId, regex, capitalize = true) {

    const elem = document.getElementById(inputId);

    if (!elem) return; // Vérifie si l'élément existe

    // Vérifie si la valeur actuelle est valide au chargement
    const isValid = regex.test(elem.value);

    if (isValid) {
        elem.classList.add("focus:border-green-700");
    }

    // Ajoute le pattern au champ input
    const regexHTML = String(regex).slice(1, -1);
    elem.setAttribute("pattern", regexHTML);

    // Ajoute un écouteur d'événements sur l'input
    elem.addEventListener("input", () => {
        const isValid = regex.test(elem.value);

        if (capitalize) {
            elem.value = elem.value.charAt(0).toUpperCase() + elem.value.slice(1);
        }

        if (isValid) {
            elem.classList.add("focus:border-green-700");
            elem.classList.remove("focus:border-red-700");
        } else {
            elem.classList.add("focus:border-red-700");
            elem.classList.remove("focus:border-green-700");
        }
    });
};


/**
 * Ajoute le nom du locataire actuel à une div
 * @param {*} property 
 */
  window.addTenant = function(property){
    let div = document.getElementById("tenant");
    let spinner = document.getElementById("full-spinner");
    div.innerHTML="";
    spinner.classList.remove('hidden');
    fetch('/app/propertyrent/gettenant/'+property, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify({ token: '1234' })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                spinner.classList.add("hidden");
                div.innerHTML = '<p>Locataire actuel :</p>'+data.message;
                // setTimeout(function(){ location.reload(); }, 2500);
            } else {
                spinner.classList.add("hidden");
                div.innerHTML = "❌ "+data.message;
                // setTimeout(function(){ location.reload(); }, 2500);
            }
        })
        .catch(error => {
                spinner.classList.add("hidden");
                div.innerHTML = "❌ Une erreur est survenue";
                // setTimeout(function(){ location.reload(); }, 2500);
        });  
}



/**
 * Ajoute une année à une input depuis une date d'un autre input
 * @param {*} input1  Date input default
 * @param {*} input2  date input à ajouter
 */
window.addOneYearToInput =  function (input1, input2) {
    let startDateInput = input1;
    let endDateInput = input2;

    function getFirstDayOfNextMonth() {
        let today = new Date();
        return new Date(today.getFullYear(), today.getMonth() + 1, 2);
    }

    function updateEndDate() {
        if (startDateInput.value) {
            let startDate = new Date(startDateInput.value);
            startDate.setFullYear(startDate.getFullYear() + 1);
            startDate.setDate(startDate.getDate() - 1); // Soustrait un jour
            endDateInput.value = startDate.toISOString().split('T')[0];
        }
    }

    // Définir automatiquement la date de début au premier jour du mois suivant
    let firstDayNextMonth = getFirstDayOfNextMonth();
    startDateInput.value = firstDayNextMonth.toISOString().split('T')[0];

    // Mettre à jour la date de fin automatiquement
    updateEndDate();

    startDateInput.addEventListener("change", updateEndDate);
};



