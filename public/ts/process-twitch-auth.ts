import getEnv from "./lib/env/env";
import parseTwitchAuthResponse from "./lib/parse-twitch-auth-response";
import getTwitchUser from "./lib/get-twitch-user";
import { API } from "./api";



const env = getEnv();

const error: HTMLElement | null = document.querySelector<HTMLElement>("[data-server-error]");



async function main() {
    const auth = parseTwitchAuthResponse();

    if (auth.type === "error") {
        //todo display error
        console.error(auth.description);
        return;
    }

    const user = await getTwitchUser(env, auth.accessToken);

    if (user.type === "error") {
        //todo display error
        console.error(user.description);
        return;
    }

    const res = await fetch(env.ORIGIN + "/user/exists?id=" + user.id);

    if (!res.ok) {
        console.log(res);
        return;
    }

    const data = await res.json() as API["user_exists"];

    if (!data.exists) {
        console.log("registering...");
        const register = await fetch(env.ORIGIN + "/auth/register", {
            method: "post",
            body: JSON.stringify({
                id: user.id,
                username: user.display_name,
                profile_picture: user.profile_image_url,
                state: auth.state
            })
        });

        if (!register.ok) {
            //todo display error
            console.log(register);
            return;
        }
    }

    const session = await fetch(env.ORIGIN + "/auth/session", {
        method: "post",
        body: JSON.stringify({
            id: user.id,
            state: auth.state
        })
    });

    const next = await session.json() as API["session_create"];

    if (next.redirect !== undefined) {
        location.replace(next.redirect);
    }

    console.log(next);
}



main().then();