import getEnv from "./lib/env/env";
const env = getEnv();
const stateURL = new URL(env.ORIGIN + "/auth/state");
stateURL.searchParams.set("r", location.href);
document.querySelector(".login")?.addEventListener("click", async () => {
    const res = await fetch(stateURL);
    if (!res.ok) {
        console.error("Could not initialize login procedure.");
        return;
    }
    const { state } = await res.json();
    localStorage.setItem("s", state);
    location.replace(env.ORIGIN + "/auth/login?s=" + state);
});
//# sourceMappingURL=pasterino.js.map