// --- Chargement du script si la page a été chargé complètement
document.addEventListener('DOMContentLoaded', function () {
    // --- Modification (Utilisateur existant)
    const editButtons = document.querySelectorAll('.edit-user-btn');
    editButtons.forEach(button => {
        button.addEventListener('click', function () {
            const userId = this.dataset.userId;
            window.location.href = `index.php?controller=admin&action=editUser&id=${userId}`;
        });
    });

    // --- Suppression (Utilisateur existant)
    const deleteAccountButtons = document.querySelectorAll('.delete-user-account-btn');
    deleteAccountButtons.forEach(button => {
        button.addEventListener('click', function () {
            if (confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')) {
                const userId = this.dataset.userId;
                window.location.href = `index.php?controller=admin&action=deleteUser&id=${userId}`;
            }
        });
    });

    // --- Approbation (Utilisateur en attente de vérification)
    const approveButtons = document.querySelectorAll('.approve-pending-btn');
    approveButtons.forEach(button => {
        button.addEventListener('click', function () {
            if (confirm('Êtes-vous sûr de vouloir approuver cet utilisateur ?')) {
                const pendingId = this.dataset.pendingId;
                window.location.href = `index.php?controller=admin&action=approveRegistration&id=${pendingId}`;
            }
        });
    });

    // --- Suppression (Utilisateur en attente de vérification)
    const deletePendingButtons = document.querySelectorAll('.delete-pending-btn');
    deletePendingButtons.forEach(button => {
        button.addEventListener('click', function () {
            if (confirm('Êtes-vous sûr de vouloir supprimer cette demande ?')) {
                const pendingId = this.dataset.pendingId;
                window.location.href = `index.php?controller=admin&action=deleteRegistration&id=${pendingId}`;
            }
        });
    });
});
