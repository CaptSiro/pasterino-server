import { localhost } from "./localhost";
import { production } from "./production";



export type Env = {
    ORIGIN: string
}



export default function getEnv(): Env {
    if (location.host === "localhost") {
        return localhost;
    }

    return production;
}