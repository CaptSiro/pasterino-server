import { localhost } from "./localhost";
import { production } from "./production";



export type Env = {
    ORIGIN: string,
    OAUTH_CLIENT_ID: string,
    OAUTH_REDIRECT_URI: string
}



export default function getEnv(): Env {
    if (location.host === "localhost") {
        return localhost;
    }

    return production;
}