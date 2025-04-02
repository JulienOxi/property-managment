import { Controller } from '@hotwired/stimulus';

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ["input"];

    connect() {
        this.updateValues();
    }

    updateValues() {
        let total1Month = 0;
        let total3Months = 0;
        let total12Months = 0;

        this.inputTargets.forEach(input => {
            const id = input.dataset.id;
            const value = parseFloat(input.value) || 0;
            total1Month += value;

            const field3Months = this.element.querySelector(`[data-id="${id}3months"]`);
            const field12Months = this.element.querySelector(`[data-id="${id}12months"]`);

            if (field3Months) field3Months.value = (value * 3).toFixed(2);
            if (field12Months) field12Months.value = (value * 12).toFixed(2);
        });

        document.getElementById("total-1-month").textContent = total1Month.toFixed(2);
        document.getElementById("total-3-month").textContent = (total1Month * 3).toFixed(2);
        document.getElementById("total-12-month").textContent = (total1Month * 12).toFixed(2);
    }
}
