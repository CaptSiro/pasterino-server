import getEnv from "./lib/env/env";
import createTwitchAuthURL from "./lib/create-twitch-auth-url";



const env = getEnv();



document.querySelector("[data-server-login-with-twitch]")?.addEventListener("click", async () => {
    const state = new URL(location.href).searchParams.get("s");

    if (state === null) {
        return;
    }

    location.replace(createTwitchAuthURL(env, state));
});