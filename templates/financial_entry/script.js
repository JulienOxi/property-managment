document.addEventListener('DOMContentLoaded', () => {
    const typeField = document.querySelector('[data-type-field]');
    const categoryField = document.querySelector('[data-category-field]');

    if (typeField && categoryField) {
        typeField.addEventListener('change', (event) => {
            const selectedType = event.target.value;

            // Exemple des catégories en fonction des types
            const categoriesByType = {
                'Entrée': [
                    { value: 'RENT', label: 'Loyer' },
                    { value: 'PARKING', label: 'Place de parc' },
                    { value: 'CHARGES', label: 'Charges' },
                    { value: 'MISCELLANEOUS_INCOME', label: 'Revenu divers' }
                ],
                'Sortie': [
                    { value: 'WATER', label: 'Eau' },
                    { value: 'HEATER', label: 'Chauffage' },
                    { value: 'MORTAGE', label: 'Hypothèque' },
                    { value: 'TAXES', label: 'Impôts' },
                    { value: 'MAINTENANCE', label: 'Maintenance' },
                    { value: 'INSURANCE', label: 'Assurance' },
                    { value: 'MISCELLANEOUS_EXPENSE', label: 'Dépenses diverses' }
                ],
                'Transfère': [
                    { value: 'BANK_TRANSFER', label: 'Transfère bancaire' }
                ]
            };

            // Vider le champ catégorie
            categoryField.innerHTML = '<option value="">Sélectionnez une catégorie</option>';

            // Ajouter les nouvelles options
            if (categoriesByType[selectedType]) {
                categoriesByType[selectedType].forEach(category => {
                    const option = document.createElement('option');
                    option.value = category.value;
                    option.textContent = category.label;
                    categoryField.appendChild(option);
                });
            }
        });
    }
});