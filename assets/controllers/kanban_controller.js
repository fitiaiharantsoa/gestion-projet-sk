import { Controller } from '@hotwired/stimulus';
import Sortable from 'sortablejs';

export default class extends Controller {
  static targets = ['column', 'task'];

  connect() {
    console.log('Kanban controller connected');
    this.initializeSortable();
  }

  initializeSortable() {
    this.columnTargets.forEach(column => {
      new Sortable(column, {
        group: 'kanban-tasks',
        animation: 150,
        ghostClass: 'sortable-ghost',
        chosenClass: 'sortable-chosen',
        dragClass: 'sortable-drag',
        onEnd: this.handleDragEnd.bind(this),
      });
    });
  }

  handleDragEnd(evt) {
    const taskId = evt.item.dataset.taskId;
    const newStatus = evt.to.dataset.status;
    const oldStatus = evt.from.dataset.status;

    if (newStatus !== oldStatus) {
      console.log(`Tâche ${taskId} déplacée de "${oldStatus}" vers "${newStatus}"`);

      fetch(`/task/${taskId}/update-status`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': this.getCsrfToken(),
        },
        body: JSON.stringify({ status: newStatus }),
      })
      .then(res => {
        if (!res.ok) throw new Error('Erreur lors de la mise à jour du statut');
        return res.json();
      })
      .then(data => {
        console.log('Statut mis à jour:', data);
        this.updateTaskCount();
      })
      .catch(err => {
        console.error(err);
        alert('Erreur lors de la mise à jour du statut de la tâche');
        // Optionnel : revert drag si erreur (plus compliqué)
      });
    }
  }

  updateTaskCount() {
    this.columnTargets.forEach(column => {
      const countSpan = column.parentElement.querySelector('.task-count');
      const taskCount = column.querySelectorAll('[data-kanban-target="task"]').length;
      if (countSpan) {
        countSpan.textContent = taskCount;
      }
    });
  }

  getCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
  }
}
