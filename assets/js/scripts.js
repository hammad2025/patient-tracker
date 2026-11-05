// assets/js/scripts.js
// Fonctions JS communes à tout le site Patient Tracker

/**
 * Confirmation générique avant suppression + requête AJAX.
 * @param {string} url - endpoint supprimer.php
 * @param {string} fieldName - nom du champ POST (ex: id_patient)
 * @param {number} id - identifiant de la ressource
 * @param {string} message - message de confirmation personnalisé
 */
function confirmerSuppression(url, fieldName, id, message) {
    if (!confirm(message || 'Êtes-vous sûr de vouloir supprimer cet élément ?')) {
        return;
    }
    fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: fieldName + '=' + id
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Erreur lors de la suppression');
        }
    })
    .catch(error => {
        alert('Erreur réseau lors de la suppression');
        console.error('Error:', error);
    });
}

document.addEventListener('DOMContentLoaded', function () {
    // Toggle visuel du rôle sur la page d'inscription
    const roleInputs = document.querySelectorAll('input[name="role"]');
    if (roleInputs.length) {
        const medecinFields = document.getElementById('champsMedecin');
        const toggleFields = function () {
            const selected = document.querySelector('input[name="role"]:checked').value;
            if (medecinFields) {
                medecinFields.style.display = (selected === 'medecin') ? 'block' : 'none';
            }
        };
        roleInputs.forEach(input => input.addEventListener('change', toggleFields));
        toggleFields();
    }

    // Auto-dismiss des alertes après 5 secondes
    document.querySelectorAll('.alert-auto-dismiss').forEach(function (alertEl) {
        setTimeout(function () {
            alertEl.classList.remove('show');
            alertEl.classList.add('fade');
            setTimeout(() => alertEl.remove(), 300);
        }, 5000);
    });
});
