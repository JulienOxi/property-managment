import { Controller } from '@hotwired/stimulus';
import Chart from "chart.js/auto";

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['chart']
    static values = {
        api: String,
        type: String
    }

    connect() {
        if (!this.apiValue) {
            console.error("Aucune URL API spécifiée !");
            return;
        }
        this.fetchData();
    }


    async fetchData() {
        try {
            const response = await fetch(this.apiValue);
            const data = await response.json();
            this.createChart(data);
        } catch (error) {
            console.error("Erreur de chargement des statistiques", error);
        }
    }
    


    createChart(data) {
        const ctx = this.element;
        new Chart(ctx, {
            type: this.typeValue,
            data: {
                labels: [data.nom],
                datasets: [{
                    label: "Statistiques financières",
                    data: [data.value],
                    backgroundColor: ["#4CAF50"],
                }]
            }
        });
    }

}
