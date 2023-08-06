import { localhost } from "./localhost";
import { production } from "./production";
export default function getEnv() {
    if (location.host === "localhost") {
        return localhost;
    }
    return production;
}
//# sourceMappingURL=env.js.map