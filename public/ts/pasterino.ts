import getEnv from "./lib/env/env";
import { API } from "./api";



const env = getEnv();

const stateURL = new URL(env.ORIGIN + "/auth/state");
stateURL.searchParams.set("r", location.href);



document.querySelector(".login")?.addEventListener("click", async () => {
    const res = await fetch(stateURL);

    if (!res.ok) {
        alert("Could not initialize login procedure.");
        return;
    }

    const { state } = await res.json() as API["state"];

    localStorage.setItem("s", state);

    location.replace(env.ORIGIN + "/auth/login?s=" + state);
});