/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';
//fonction de validation des formulaire
// Cette fonction prend l'ID du champ d'entrée (inputId) et une expression régulière (regex) comme paramètres.
//exemple :   validateInput("_postal_code", '/^\d{5}$/');

// Fonction pour valider un champ d'entrée basé sur une expression régulière
function validateInput(inputId, regex) {
    const input = document.getElementById(inputId);
    
    // if (!input) return; // Assurez-vous que l'élément existe


    input.addEventListener("input", () => {
    const isValid = regex.test(input.value);

    // Ajouter ou supprimer les classes en fonction de la validité
    input.classList.toggle("focus:border-green-700", isValid);
    input.classList.toggle("focus:border-red-700", !isValid);

    //ajoute le regex au input
    let stringRegex = String(regex);
    let regexHTML = stringRegex.slice(1,-1);
    elem.setAttribute("pattern", regexHTML);

    });
};


