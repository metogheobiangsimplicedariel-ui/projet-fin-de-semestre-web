    <footer class="bg-gray-800 text-white mt-0 pt-12 pb-8 z-0 relative">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-lg font-semibold mb-4 text-studify-blue">Gestion des Notes</h3>
                    <p class="text-gray-400 text-sm leading-relaxed">
                        Système de gestion des notes universitaires complet, permettant un suivi rigoureux et transparent du parcours étudiant.
                    </p>
                    <p class="text-gray-500 mt-4 text-xs">
                        &copy; <?php echo date('Y'); ?> Université - Projet Académique
                    </p>
                </div>

                <div>
                    <h3 class="text-lg font-semibold mb-4 text-studify-blue">Liens rapides</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="?page=help" class="text-gray-400 hover:text-studify-blue transition">Aide & Support</a></li>
                        <li><a href="?page=contact" class="text-gray-400 hover:text-studify-blue transition">Contact administration</a></li>
                        <li><a href="?page=privacy" class="text-gray-400 hover:text-studify-blue transition">Politique de confidentialité</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-lg font-semibold mb-4 text-studify-blue">Contact</h3>
                    <p class="text-gray-400 text-sm mb-2">
                        <i class="fas fa-envelope mr-3 w-4"></i> support@universite.fr
                    </p>
                    <p class="text-gray-400 text-sm">
                        <i class="fas fa-phone mr-3 w-4"></i> 01 23 45 67 89
                    </p>
                    <div class="mt-4 flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-500 text-xs">
                <p>Version 1.0.0 - Développé en PHP/MySQL avec Tailwind CSS</p>
            </div>
        </div>
    </footer>

    <?php if (isset($pageScript)): ?>
        <script src="<?php echo $pageScript; ?>"></script>
    <?php endif; ?>
    <script>
        // 1. Initialiser le Drag & Drop
        const el = document.getElementById('sortableTable');
        const sortable = Sortable.create(el, {
            handle: '.handle', // On ne peut bouger qu'en cliquant sur l'icône poignée
            animation: 150,
            ghostClass: 'bg-blue-50', // Classe quand on déplace
            onEnd: function(evt) {
                // Quand on lâche, on envoie le nouvel ordre au serveur
                let order = [];
                document.querySelectorAll('#sortableTable tr').forEach(function(row) {
                    order.push(row.getAttribute('data-id'));
                });

                // Envoi AJAX (Fetch)
                const formData = new FormData();
                formData.append('action', 'reorder_columns');
                // Astuce : on envoie un tableau PHP via FormData
                order.forEach((id, index) => {
                    formData.append(`order[${index}]`, id);
                });

                fetch('index.php?page=admin_config_cols&periode=<?= $periode_id ?>&matiere=<?= $current_matiere_id ?>', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Ordre sauvegardé');
                    })
                    .catch(error => console.error('Erreur:', error));
            }
        });

        // 2. Gestion du Modal Modifier (Remplissage)
        function openEditModal(colData) {
            document.getElementById('edit_column_id').value = colData.id;
            document.getElementById('edit_nom').value = colData.nom_colonne;
            document.getElementById('edit_code').value = colData.code_colonne;
            document.getElementById('edit_max').value = colData.note_max;
            document.getElementById('edit_coeff').value = colData.coefficient;
            document.getElementById('edit_type').value = colData.type;

            document.getElementById('modalEditColumn').classList.remove('hidden');
        }

        // Code pour le toggle password - À placer APRÈS votre formulaire
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Initialisation du toggle password...');

            const toggleButtons = document.querySelectorAll('.toggle-password');
            console.log('Nombre de boutons trouvés:', toggleButtons.length);

            toggleButtons.forEach(button => {
                console.log('Configuration du bouton:', button);

                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    console.log('Bouton cliqué!');
                    const targetSelector = this.getAttribute('data-target');
                    console.log('Target selector:', targetSelector);

                    const passwordInput = document.querySelector(targetSelector);
                    console.log('Input trouvé:', passwordInput);

                    if (!passwordInput) {
                        console.error('ERREUR: Input non trouvé avec le sélecteur:', targetSelector);
                        return;
                    }

                    const icon = this.querySelector('i');
                    console.log('Icône trouvée:', icon);

                    // Toggle du type de l'input
                    if (passwordInput.type === 'password') {
                        passwordInput.type = 'text';
                        if (icon) {
                            icon.classList.remove('fa-eye');
                            icon.classList.add('fa-eye-slash');
                        }
                        this.setAttribute('aria-label', 'Masquer le mot de passe');
                        console.log('Mot de passe affiché');
                    } else {
                        passwordInput.type = 'password';
                        if (icon) {
                            icon.classList.remove('fa-eye-slash');
                            icon.classList.add('fa-eye');
                        }
                        this.setAttribute('aria-label', 'Afficher le mot de passe');
                        console.log('Mot de passe masqué');
                    }

                    // Remettre le focus
                    passwordInput.focus();
                });
            });

            console.log('Toggle password initialisé avec succès');
        });

        // Test manuel - à exécuter dans la console
        function testTogglePassword() {
            const btn = document.querySelector('.toggle-password');
            const input = document.querySelector('#login_pass');

            if (btn && input) {
                console.log('Test: Bouton et input trouvés');
                console.log('Type actuel:', input.type);

                // Simuler un clic
                btn.click();

                setTimeout(() => {
                    console.log('Type après clic:', input.type);
                }, 100);
            } else {
                console.error('Test échoué: éléments non trouvés');
                console.log('Bouton:', btn);
                console.log('Input:', input);
            }
        }
    </script>
    </body>

    </html>