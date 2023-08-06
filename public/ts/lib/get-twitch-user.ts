import { Env } from "./env/env";



export type TwitchUserSuccess = {
    type: "success",
    profile_image_url: string,
    display_name: string,
    id: number
}

export type TwitchUserError = {
    type: "error",
    description: string
}

export type TwitchUser = TwitchUserError | TwitchUserSuccess



export default async function getTwitchUser(env: Env, accessToken: string): Promise<TwitchUser> {
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