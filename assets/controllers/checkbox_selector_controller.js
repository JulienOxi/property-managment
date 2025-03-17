import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ["checkboxes"];

    toggleSelection() {
        const checkboxes = this.checkboxesTarget.querySelectorAll('input[type="checkbox"]');
        console.log(checkboxes);
        const allChecked = [...checkboxes].every(checkbox => checkbox.checked);

        checkboxes.forEach(checkbox => {
            checkbox.checked = !allChecked;
        });
    }
}
