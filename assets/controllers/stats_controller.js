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

    // Fonction pour récupérer les noms des mois jusqu'au mois actuel
    getMonthNamesUpToNow() {
        const monthNames = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'];
        const currentMonth = new Date().getMonth(); // Numéro du mois actuel (0-11)
        return monthNames.slice(0, currentMonth + 1); // Retourne les mois jusqu'à aujourd'hui
    }
    


    createChart(data) {
        const ctx = this.element;
        new Chart(ctx, {
            type: this.typeValue,
            data: {
                labels: this.getMonthNamesUpToNow(),
                datasets: [
                    {
                        label: 'Revenus',
                        data: JSON.parse(data.incomeByMonth),
                        backgroundColor: 'rgba(75, 192, 192, 0.5)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Dépenses',
                        data: JSON.parse(data.expensesByMonth),
                        backgroundColor: 'rgba(255, 99, 132, 0.5)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

}
