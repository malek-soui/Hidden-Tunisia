// Fonction de validation pour le formulaire d'ajout de lieu
function validerAjoutLieu() {
    let nom = document.getElementById('nom_lieu').value.trim();
    let description = document.getElementById('description_lieu').value.trim();
    let region = document.getElementById('region_lieu').value;
    let categorie = document.getElementById('categorie_lieu').value;

    if (nom === "") {
        alert("Veuillez saisir le nom du lieu.");
        return false;
    }
    if (description === "") {
        alert("Veuillez saisir une description.");
        return false;
    }
    if (region === "Destination Area" || region === "") {
        alert("Veuillez sélectionner une région.");
        return false;
    }
    if (categorie === "Destination Type" || categorie === "") {
        alert("Veuillez sélectionner un type de destination.");
        return false;
    }

    return true; // Le formulaire est valide
}

// Fonction de validation pour le formulaire de contact
function validerContact() {
    let nom = document.getElementById('nom_contact').value.trim();
    let email = document.getElementById('email_contact').value.trim();
    let message = document.getElementById('message_contact').value.trim();

    if (nom === "") {
        alert("Veuillez saisir votre nom.");
        return false;
    }
    if (email === "") {
        alert("Veuillez saisir votre email.");
        return false;
    }
    
    // Contrôle basique de l'email
    if (email.indexOf("@") === -1 || email.indexOf(".") === -1) {
        alert("Veuillez saisir un email valide contenant un @ et un point.");
        return false;
    }

    if (message === "") {
        alert("Veuillez saisir votre message.");
        return false;
    }

    return true;
}

// Fonction de validation pour le login/register
function validerAuth() {
    let email = document.getElementById('email_auth').value.trim();
    let mdp = document.getElementById('mdp_auth').value.trim();

    if (email === "" || mdp === "") {
        alert("Veuillez remplir tous les champs.");
        return false;
    }
    return true;
}
