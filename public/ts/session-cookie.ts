import getEnv from "./lib/env/env";



const env = getEnv();



fetch(env.ORIGIN + "/auth/set-cookie?s=" + localStorage.getItem("s"))
    .then();