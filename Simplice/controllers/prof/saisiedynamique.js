// assets/professeur/js/saisie_dynamique.js
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('.note-input');
    const sauvegardeEnCours = new Set();
    
    // Configuration
    const DEBOUNCE_DELAY = 1000; // 1 seconde
    const AUTO_SAVE_ENABLED = true;
    
    // Navigation clavier
    inputs.forEach(input => {
        input.addEventListener('keydown', function(e) {
            const currentCell = this.parentElement.parentElement;
            const currentRow = currentCell.parentElement;
            const currentIndex = Array.from(currentRow.children).indexOf(currentCell);
            
            switch(e.key) {
                case 'Enter':
                    e.preventDefault();
                    saveNote(this);
                    // Aller à la cellule suivante (ligne suivante)
                    const nextRow = currentRow.nextElementSibling;
                    if (nextRow) {
                        const nextInput = nextRow.children[currentIndex].querySelector('.note-input');
                        if (nextInput) nextInput.focus();
                    }
                    break;
                    
                case 'Tab':
                    if (!e.shiftKey) {
                        e.preventDefault();
                        saveNote(this);
                        // Cellule suivante
                        const nextCell = currentCell.nextElementSibling;
                        if (nextCell) {
                            const nextInput = nextCell.querySelector('.note-input');
                            if (nextInput) nextInput.focus();
                        }
                    }
                    break;
                    
                case 'ArrowUp':
                case 'ArrowDown':
                case 'ArrowLeft':
                case 'ArrowRight':
                    e.preventDefault();
                    handleArrowNavigation(e.key, currentCell, currentRow);
                    break;
            }
        });
        
        // Validation en temps réel
        input.addEventListener('input', function() {
            validateNoteInput(this);
        });
        
        // Sauvegarde automatique
        input.addEventListener('blur', function() {
            if (AUTO_SAVE_ENABLED && this.value.trim() !== '') {
                debouncedSave(this);
            }
        });
    });
    
    // Gestion des statuts spéciaux
    document.querySelectorAll('.statut-note').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const statut = this.getAttribute('data-statut');
            const input = this.closest('.input-group').querySelector('.note-input');
            
            input.value = statut;
            saveNote(input);
            
            // Afficher le statut
            const statutSpan = document.createElement('small');
            statutSpan.className = 'text-muted d-block';
            statutSpan.textContent = statut;
            
            const existing = input.parentElement.nextElementSibling;
            if (existing && existing.classList.contains('text-muted')) {
                existing.remove();
            }
            
            input.parentElement.parentElement.appendChild(statutSpan);
        });
    });
    
    // Effacer une note
    document.querySelectorAll('.effacer-note').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const input = this.closest('.input-group').querySelector('.note-input');
            input.value = '';
            saveNote(input);
        });
    });
    
    // Fonction de validation
    function validateNoteInput(input) {
        const value = input.value.trim();
        const max = parseFloat(input.getAttribute('data-note-max'));
        
        if (value === '' || ['ABS', 'DIS', 'DEF'].includes(value.toUpperCase())) {
            input.classList.remove('is-invalid', 'is-valid');
            return true;
        }
        
        const numValue = parseFloat(value);
        if (isNaN(numValue)) {
            input.classList.add('is-invalid');
            input.classList.remove('is-valid');
            return false;
        }
        
        if (numValue < 0 || numValue > max) {
            input.classList.add('is-invalid');
            input.classList.remove('is-valid');
            return false;
        }
        
        input.classList.remove('is-invalid');
        input.classList.add('is-valid');
        return true;
    }
    
    // Fonction de sauvegarde
    function saveNote(input) {
        if (!validateNoteInput(input)) {
            showMessage('Valeur invalide', 'error');
            return;
        }
        
        const etudiantId = input.getAttribute('data-etudiant-id');
        const colonneId = input.getAttribute('data-colonne-id');
        const valeur = input.value.trim();
        
        // Déterminer le statut
        let statut = 'saisie';
        if (['ABS', 'DIS', 'DEF'].includes(valeur.toUpperCase())) {
            statut = valeur.toLowerCase();
        }
        
        // Empêcher les doublons
        const key = `${etudiantId}-${colonneId}`;
        if (sauvegardeEnCours.has(key)) {
            return;
        }
        sauvegardeEnCours.add(key);
        
        // Afficher l'indicateur de sauvegarde
        input.classList.add('saving');
        
        // Envoyer la requête AJAX
        const formData = new FormData();
        formData.append('etudiant_id', etudiantId);
        formData.append('colonne_id', colonneId);
        formData.append('valeur', valeur);
        formData.append('statut', statut);
        
        fetch('index.php?action=sauvegarder_note', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                input.classList.remove('saving');
                input.classList.add('saved');
                setTimeout(() => {
                    input.classList.remove('saved');
                }, 2000);
            } else {
                showMessage(data.message, 'error');
                input.classList.remove('saving');
            }
        })
        .catch(error => {
            showMessage('Erreur réseau: ' + error.message, 'error');
            input.classList.remove('saving');
        })
        .finally(() => {
            sauvegardeEnCours.delete(key);
        });
    }
    
    // Debounce pour éviter trop de requêtes
    let debounceTimer;
    function debouncedSave(input) {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            saveNote(input);
        }, DEBOUNCE_DELAY);
    }
    
    // Navigation avec les flèches
    function handleArrowNavigation(key, currentCell, currentRow) {
        let nextCell;
        
        switch(key) {
            case 'ArrowUp':
                const prevRow = currentRow.previousElementSibling;
                if (prevRow) {
                    nextCell = prevRow.children[Array.from(currentRow.children).indexOf(currentCell)];
                }
                break;
                
            case 'ArrowDown':
                const nextRow = currentRow.nextElementSibling;
                if (nextRow) {
                    nextCell = nextRow.children[Array.from(currentRow.children).indexOf(currentCell)];
                }
                break;
                
            case 'ArrowLeft':
                nextCell = currentCell.previousElementSibling;
                break;
                
            case 'ArrowRight':
                nextCell = currentCell.nextElementSibling;
                break;
        }
        
        if (nextCell) {
            const nextInput = nextCell.querySelector('.note-input');
            if (nextInput && !nextInput.disabled) {
                nextInput.focus();
                saveNote(currentCell.querySelector('.note-input'));
            }
        }
    }
    
    // Afficher les messages
    function showMessage(message, type) {
        const modal = new bootstrap.Modal(document.getElementById('modal-sauvegarde'));
        const body = document.getElementById('message-sauvegarde');
        
        body.innerHTML = `<div class="alert alert-${type === 'error' ? 'danger' : 'success'}">${message}</div>`;
        modal.show();
        
        setTimeout(() => {
            modal.hide();
        }, 3000);
    }
    
    // Raccourcis clavier globaux
    document.addEventListener('keydown', function(e) {
        // Ctrl+S pour sauvegarder toutes les modifications
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            saveAllNotes();
        }
        
        // Échap pour annuler
        if (e.key === 'Escape') {
            const focused = document.activeElement;
            if (focused.classList.contains('note-input')) {
                focused.blur();
            }
        }
    });
    
    // Sauvegarder toutes les notes
    function saveAllNotes() {
        const inputs = document.querySelectorAll('.note-input:not(:disabled)');
        let saved = 0;
        
        inputs.forEach(input => {
            if (input.value.trim() !== '') {
                saveNote(input);
                saved++;
            }
        });
        
        showMessage(`${saved} notes sauvegardées`, 'success');
    }
});