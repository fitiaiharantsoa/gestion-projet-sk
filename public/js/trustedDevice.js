// Fonction pour générer un UUID v4
function generateUUID() {
    // https://stackoverflow.com/questions/105034/how-to-create-a-guid-uuid
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
        let r = Math.random() * 16 | 0,
            v = c === 'x' ? r : (r & 0x3 | 0x8);
        return v.toString(16);
    });
}

// Fonction pour lire un cookie par son nom
function getCookie(name) {
    let value = "; " + document.cookie;
    let parts = value.split("; " + name + "=");
    if (parts.length === 2) return parts.pop().split(";").shift();
}

// Fonction pour définir un cookie
function setCookie(name, value, days) {
    let expires = "";
    if (days) {
        let date = new Date();
        date.setTime(date.getTime() + (days*24*60*60*1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "")  + expires + "; path=/; SameSite=Lax";
}

// Vérifie si le cookie existe, sinon le crée
function ensureTrustedDeviceToken() {
    let token = getCookie('trusted_device_token');
    if (!token) {
        token = generateUUID();
        setCookie('trusted_device_token', token, 30); // expiration 30 jours
        console.log('Trusted device token created:', token);
    } else {
        console.log('Trusted device token exists:', token);
    }
}

// Appelle la fonction au chargement de la page
ensureTrustedDeviceToken();
