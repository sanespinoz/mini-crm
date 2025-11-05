import Echo from "laravel-echo";

window.Echo = new Echo({
    broadcaster: "reverb",
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT,
    wssPort: import.meta.env.VITE_REVERB_PORT,
    forceTLS: false,
    enabledTransports: ["ws", "wss"],
});

const contactId = 1;

window.Echo.channel(`contacts.${contactId}`).listen(
    "ContactScoreProcessed",
    (data) => {
        console.log("Score actualizado:", data);
    }
);
