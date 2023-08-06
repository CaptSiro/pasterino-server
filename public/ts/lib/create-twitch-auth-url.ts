import { Env } from "./env/env";



export default function createTwitchAuthURL(env: Env, state: string): URL {
    const url = new URL("https://id.twitch.tv/oauth2/authorize");

    url.searchParams.set("response_type", "token");
    url.searchParams.set("client_id", env.OAUTH_CLIENT_ID);
    url.searchParams.set("redirect_uri", env.OAUTH_REDIRECT_URI);
    url.searchParams.set("scope", "user_read");
    url.searchParams.set("state", state);

    return url;
}