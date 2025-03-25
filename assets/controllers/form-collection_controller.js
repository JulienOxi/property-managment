import { Controller } from '@hotwired/stimulus';

/* stimulusFetch: 'lazy' */
export default class extends Controller {

    static values = {
        btnadd: String
    }

    connect() {
        console.log(this.btnaddValue);
        this.index = this.element.childElementCount;
        const btn = document.createElement('button');
        btn.setAttribute('class', 'bg-teal-500 text-white px-4 py-2 rounded shadow hover:bg-teal-600');
        btn.textContent = this.btnaddValue ? this.btnaddValue : 'Ajouter un locataire';
        btn.setAttribute('type', 'button');
        btn.addEventListener('click', this.addelement);
        this.element.childNodes.forEach(this.addDeleteButton);
        this.element.insertAdjacentElement('afterend', btn);
    }

    /**
     * Ajoute un nouvel élément au formulaire imbriqué
     * @param {MouseEvent} e 
     */
    addelement = (e) => {
        e.preventDefault();

        // Génération de l'élément à partir du prototype
        const element = document.createRange().createContextualFragment(
            this.element.dataset['prototype'].replaceAll('__name__', this.index)
        ).firstElementChild;

        // Application des styles de la macro aux champs
        this.applyFieldStyles(element);

        this.addDeleteButton(element);
        this.index++;
        this.element.append(element);
    }

    /**
     * Applique les styles définis dans la macro Twig aux champs générés dynamiquement
     * @param {HTMLElement} item 
     */
    applyFieldStyles(item) {
        item.classList.add('tenant-entry', 'border', 'p-4', 'mb-4', 'rounded-lg', 'shadow', 'col-span-2');

        // Sélectionne tous les champs du formulaire et leur applique la classe input-base
        item.querySelectorAll('input, select, textarea').forEach(field => {
            field.classList.add('input-base');
        });

        // Ajoute les labels stylisés
        item.querySelectorAll('label').forEach(label => {
            label.classList.add('block', 'text-sm', 'font-medium', 'text-gray-700');
        });
    }

    /**
     * Ajoute un bouton de suppression à chaque élément
     * @param {HTMLElement} item 
     */
    addDeleteButton = (item) => {
        const btn = document.createElement('button');
        btn.setAttribute('class', 'mt-4 bg-white text-red-600 border-[1px] border-red-600 px-2 pt-1 rounded shadow hover:bg-red-600 hover:text-white');
        btn.innerHTML = '<span class="material-symbols-outlined">delete</span>';
        btn.setAttribute('type', 'button');
        item.append(btn);
        btn.addEventListener('click', e => {
            e.preventDefault();
            item.remove();
        });
    }
}
