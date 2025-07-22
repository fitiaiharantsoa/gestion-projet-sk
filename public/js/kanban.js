
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.kanban-column').forEach(function (column) {
        new Sortable(column, {
            group: 'kanban',
            animation: 150,
            onEnd: function (evt) {
                var taskId = evt.item.dataset.taskId;
                var newStatus = evt.to.dataset.status;

                fetch('/task/' + taskId + '/update-status', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ status: newStatus })
                })
                .then(function (response) {
                    if (!response.ok) {
                        throw new Error("Erreur réseau");
                    }
                    return response.json();
                })
                .then(function (data) {
                    console.log('Statut mis à jour pour la tâche ' + taskId);
                })
                .catch(function (error) {
                    console.error('Erreur lors de la mise à jour :', error);
                });
            }
        });
    })
});
</script>
