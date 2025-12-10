const btn = document.getElementById("chatbot-btn");
const windowChat = document.getElementById("chatbot-window");
const messages = document.getElementById("chatbot-messages");
const input = document.getElementById("chatbot-text");
const sendBtn = document.getElementById("chatbot-send");
const voiceBtn = document.getElementById("chatbot-voice");
// ✅ Charger l’historique au démarrage
window.addEventListener("load", () => {
    const history = localStorage.getItem("chatbot_history");
    if (history) {
        messages.innerHTML = history;
        messages.scrollTop = messages.scrollHeight;
    }
});


// Ouvrir / Fermer la fenêtre
btn.addEventListener("click", () => {
    windowChat.style.display = windowChat.style.display === "flex" ? "none" : "flex";
});

// Chargement de l'historique au démarrage
window.addEventListener("load", () => {
    const history = localStorage.getItem("chatbot_history");
    if (history) {
        messages.innerHTML = history;
        messages.scrollTop = messages.scrollHeight;
    }
});

// Bouton ENVOYER
sendBtn.addEventListener("click", sendMessage);

// Appui sur ENTER
input.addEventListener("keydown", (e) => {
    if (e.key === "Enter") sendMessage();
});

// Fonction d’envoi
function sendMessage() {
    let text = input.value.trim();
    if (text === "") return;

    messages.innerHTML += `<div class='chatbot-user'>${text}</div>`;
    input.value = "";

    fetch("/RebornArt/views/Utilisateur/chatbot_api.php", {


        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ message: text })
    })
    .then(res => res.json())
    .then(data => {
        messages.innerHTML += `<div class='chatbot-bot'>${data.reply}</div>`;
        messages.scrollTop = messages.scrollHeight;

        // ✅ Sauvegarde de l’historique
        localStorage.setItem("chatbot_history", messages.innerHTML);
    });

}

// --------------------
// BOUTONS RAPIDES
// --------------------
function quickSend(text) {
    input.value = text;
    sendMessage();
}

// --------------------
// MODE VOCAL
// --------------------
if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    const recognition = new SpeechRecognition();

    recognition.lang = "fr-FR";
    recognition.continuous = false;
    recognition.interimResults = false;

    voiceBtn.addEventListener("click", () => {
        try {
            recognition.start();
            console.log("🎤 Écoute en cours...");
        } catch (e) {
            alert("Le micro est déjà en cours d'utilisation.");
        }
    });

    recognition.onresult = function(event) {
        const voiceText = event.results[0][0].transcript;
        input.value = voiceText;
        sendMessage();
    };

    recognition.onerror = function(event) {
        alert("Erreur micro : " + event.error + "\nUtilise Chrome et autorise le micro.");
    };
} else {
    alert("Le mode vocal n'est pas supporté sur ce navigateur.");
    voiceBtn.style.display = "none";
}

// Effacer l’historique
const clearBtn = document.getElementById("clear-history");

if (clearBtn) {
    clearBtn.addEventListener("click", () => {
        if (confirm("Effacer toutes les conversations ?")) {
            localStorage.removeItem("chatbot_history");
            messages.innerHTML = "";
        }
    });
}


