import { Controller } from '@hotwired/stimulus';

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends Controller {

    static targets = ["menu"];

    connect() {
        document.addEventListener("click", this.closeAllMenus.bind(this));
    }

    toggle(event) {
        event.stopPropagation(); // Empêche la propagation pour éviter la fermeture immédiate

        // Ferme tous les autres menus
        document.querySelectorAll("[data-menu-target='menu']").forEach(menu => {
            if (menu !== this.menuTarget) {
                menu.classList.add("hidden");
            }
        });

        // Ouvre ou ferme le menu actuel
        this.menuTarget.classList.toggle("hidden");
    }

    closeAllMenus() {
        this.menuTargets.forEach(menu => menu.classList.add("hidden"));
    }

}
