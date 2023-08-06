export default async function getTwitchUser(env, accessToken) {
    const response = await fetch("https://api.twitch.tv/helix/users", {
        headers: {
            "Authorization": "Bearer " + accessToken,
            "Client-Id": env.OAUTH_CLIENT_ID
        }
    });
    if (!response.ok) {
        return {
            type: "error",
            description: "Failed to fetch username and profile picture from Twitch"
        };
    }
    const { profile_image_url, display_name, id } = (await response.json()).data[0];
    return {
        type: "success",
        profile_image_url,
        display_name,
        id: Number(id)
    };
}
//# sourceMappingURL=get-twitch-user.js.map