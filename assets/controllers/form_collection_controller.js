import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        const btn = document.createElement('button');
        btn.setAttribute('class', 'btn btn-primary');
        btn.textContent = 'Ajouter un locataire';
        btn.setAttribute('type', 'button');
        btn.addEventListener('click', this.addelement);
        this.element.append(btn);
    }

/**
 * 
 * @param {MouseEvent} e 
 */
    addelement = (e) => {
        e.preventDefault();
    }
}