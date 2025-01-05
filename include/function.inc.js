// Fonction pour basculer le type du champ mot de passe
    function togglePassword(fieldId, show) {
        const passwordField = document.getElementById(fieldId);
        if (show) {
            passwordField.type = 'text'; 
        } else {
            passwordField.type = 'password';
        }
    }

// Mise en place de l'immobilisation du footer
document.addEventListener('DOMContentLoaded', () => {
    const footer = document.querySelector('footer');
    const mainContent = document.querySelector('main');

    window.addEventListener('scroll', () => {
        const mainBottom = mainContent.getBoundingClientRect().bottom;
        const windowHeight = window.innerHeight;

        // Si le bas du main est visible, afficher le footer
        if (mainBottom <= windowHeight) { 
            footer.classList.add('visible');
        } else {
            footer.classList.remove('visible');
        }
    });
});

//Mise en place du carroussel
function scrollCarousel(direction) {
        const carousel = document.getElementById('carousel');
        const scrollAmount = 200; // Distance de défilement

        if (direction === 'left') {
            carousel.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
        } else if (direction === 'right') {
            carousel.scrollBy({ left: scrollAmount, behavior: 'smooth' });
        }
    }

// Systeme de hide when scroll du header
let lastScrollTop = 0;
window.addEventListener('scroll', function() {
    const header = document.querySelector('header');
    const currentScroll = window.scrollY;

    if (currentScroll > lastScrollTop) { 
        header.classList.add('hidden');
    } else {  
        header.classList.remove('hidden');
    }

    lastScrollTop = currentScroll <= 0 ? 0 : currentScroll; 
});

// Récupération des éléments nécessaires
document.addEventListener('DOMContentLoaded', () => {
    const switchButton = document.getElementById('modeSwitch');
    const themeLink = document.getElementById('themeStylesheet');

    // Fonction pour appliquer le mode sombre
    function enableDarkMode() {
        themeLink.href = 'stylesombre.css'; // Applique le fichier CSS sombre
        localStorage.setItem('mode', 'dark'); // Enregistre la préférence
    }

    // Fonction pour appliquer le mode clair
    function enableLightMode() {
        themeLink.href = 'style.css'; // Applique le fichier CSS clair
        localStorage.setItem('mode', 'light'); // Enregistre la préférence
    }

    // Vérification de l'état du mode au chargement
    function checkModeStatus() {
        const savedMode = localStorage.getItem('mode');
        if (savedMode === 'dark') {
            enableDarkMode();
            switchButton.checked = true; // Coche le switch pour le mode sombre
        } else {
            enableLightMode();
            switchButton.checked = false; // Laisse le switch décoché pour le mode clair
        }
    }

    // Ajouter l'événement de changement d'état du switch
    switchButton.addEventListener('change', () => {
        if (switchButton.checked) {
            enableDarkMode();
        } else {
            enableLightMode();
        }
    });

    // Vérifier l'état du mode au chargement de la page
    checkModeStatus();
});




function previewImage(event) {
    var file = event.target.files[0];
    if (file) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('profileImg').src = e.target.result; // Changer l'image prévisualisée
        };
        reader.readAsDataURL(file);
    }
}

function uploadImage(event) {
    const file = event.target.files[0];
    if (!file) return; // Si aucun fichier n'est sélectionné, on ne fait rien

    const formData = new FormData();
    formData.append("profileImage", file); // On ajoute l'image dans FormData

    // Envoi de la requête AJAX
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "upload.php", true);

    xhr.onload = function () {
        if (xhr.status === 200) {
            // Si l'upload est réussi, on met à jour l'image affichée
            const response = JSON.parse(xhr.responseText);
            if (response.status === "success") {
                document.getElementById("profileImg").src = response.imagePath;
            } else {
                alert("Erreur lors de l'upload de l'image.");
            }
        } else {
            alert("Erreur de communication avec le serveur.");
        }
    };

    xhr.send(formData); // Envoi des données au serveur
}

function enableEdit() {
    var pseudoElement = document.getElementById('pseudo');
    var currentPseudo = pseudoElement.innerText;
    pseudoElement.innerHTML = '<input type="text" id="newPseudo" value="' + currentPseudo + '" onkeypress="savePseudo(event)" />';
    var inputField = document.getElementById('newPseudo');
    inputField.style.border = 'none';            // Pas de bordure
    inputField.style.outline = 'none';           // Pas de contour
    inputField.style.background = 'transparent'; // Fond transparent
    inputField.style.font = 'inherit';          // Garder la même police que le texte
    inputField.style.color = 'inherit';         // Garder la même couleur que le texte
    inputField.style.padding = '0';              // Pas de padding
    inputField.style.width = 'auto';             // Largeur automatique basée sur le texte
    inputField.style.minWidth = '30px';          // Largeur minimale pour éviter trop petit
    inputField.style.maxWidth = '75px';         // Largeur maximale selon la taille que tu veux
    inputField.style.whiteSpace = 'nowrap';      // Pour éviter le retour à la ligne
    inputField.focus();
    inputField.setSelectionRange(currentPseudo.length, currentPseudo.length);
}



    // Fonction pour sauvegarder le pseudo lorsque l'utilisateur appuie sur "Entrée"
    function savePseudo(event) {
        if (event.key === 'Enter') {  // Vérifier si la touche "Entrée" a été pressée
            var newPseudo = document.getElementById('newPseudo').value;

            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'update_pseudo.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status == 200) {
                    // Si la mise à jour est réussie, mettre à jour l'affichage du pseudo
                    document.getElementById('pseudo').innerHTML = newPseudo + '<i class="fas fa-pencil-alt edit-icon" onclick="enableEdit()"></i>';
                    alert('Pseudo mis à jour avec succès!');
                } else {
                    alert('Erreur lors de la mise à jour du pseudo');
                }
            };
            xhr.send('pseudo=' + newPseudo);  // Envoyer le nouveau pseudo
        }
    }

function editField(fieldId) {
        var field = document.getElementById(fieldId);
        field.readOnly = false;
        field.focus(); // Placer le curseur dans le champ quand il devient modifiable
        placeCursorAtEnd(field);
    }

    function editPasswordField(fieldId) {
        var field = document.getElementById(fieldId);
        field.readOnly = false;
        field.focus(); // Placer le curseur dans le champ quand il devient modifiable
        placeCursorAtEnd(field);
    }
function placeCursorAtEnd(field) {
        var value = field.value;
        field.value = '';
        field.value = value;
    }

