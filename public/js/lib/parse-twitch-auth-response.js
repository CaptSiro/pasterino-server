export default function parseTwitchAuthResponse() {
    const search = new URL(location.href).searchParams;
    if (search.has("error")) {
        return {
            type: "error",
            description: search.get("error_description") ?? "Unknown error",
            state: search.get("state") ?? ""
        };
    }
    const res = new Map(window.location.hash
        .substring(1)
        .split("&")
        .map(pair => pair.split("=")));
    const accessToken = res.get("access_token");
    const state = res.get("state");
    if (accessToken === undefined || state === undefined) {
        return {
            type: "error",
            description: "Could not get user's access token",
            state: ""
        };
    }
    return {
        type: "success",
        accessToken,
        state
    };
}
//# sourceMappingURL=parse-twitch-auth-response.js.map