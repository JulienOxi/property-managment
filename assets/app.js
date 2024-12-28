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


window.validateInput = function(inputId, regex, capitalize = true){

    const elem = document.getElementById(inputId);
  
    console.log('test input 1');
    //test avec le regex
    const isValid = regex.test(elem.value);
    //on load check
    window.addEventListener("load", () => {
      //test avec le regex  
      if(isValid){
        elem.classList.add("focus:border-green-700");
      }

        //ajoute le regex au input
        let stringRegex = String(regex);
        let regexHTML = stringRegex.slice(1,-1);
        elem.setAttribute("pattern", regexHTML);      
    });
  
    // Event listner
    elem.addEventListener("input", () => {
        console.log('test input');
      //test avec le regex
      const isValid = regex.test(elem.value);
      if(capitalize){
        //on met la première lettre en majuscule
        let str = elem.value.charAt(0).toUpperCase() + elem.value.slice(1);
        elem.value = str;
      }
  
      if (isValid) {
            elem.classList.add("focus:border-green-700");
            elem.classList.remove("focus:border-red-700");
        }else{
            elem.classList.add("focus:border-red-700");
            elem.classList.remove("focus:border-green-700");
        }
    });
  };


