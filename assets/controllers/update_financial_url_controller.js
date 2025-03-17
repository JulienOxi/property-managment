import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static values = { baseUrl: String };

    connect() {
        console.log("Base URL récupérée :", this.baseUrlValue); // Vérifie l'URL récupérée

        this.element.querySelectorAll('input[name="transaction_filter[type]"]').forEach(field => {
            field.addEventListener("change", () => this.updateUrl());
        });

        this.element.querySelectorAll('input[name="transaction_filter[property]"]').forEach(field => {
            field.addEventListener("change", () => this.updateUrl());
        });
    }

    updateUrl() {
        // Récupération des filtres sélectionnés
        const selectedType = this.element.querySelector('input[name="transaction_filter[type]"]:checked')?.value || "";
        const selectedProperty = this.element.querySelector('input[name="transaction_filter[property]"]:checked')?.value || "";

        // Récupération des paramètres actuels
        let url = new URL(this.baseUrlValue, window.location.origin);
        let params = new URLSearchParams(url.search);

        // Mise à jour des paramètres
        if (selectedType) {
            params.set("type", selectedType);
        } else {
            params.delete("type");
        }

        if (selectedProperty) {
            params.set("property", selectedProperty);
        } else {
            params.delete("property");
        }

        // Nouvelle URL avec paramètres mis à jour
        url.search = params.toString();

        console.log("Nouvelle URL générée :", url.toString()); // Vérifie l'URL finale
        window.location.href = url.toString();
    }
}
