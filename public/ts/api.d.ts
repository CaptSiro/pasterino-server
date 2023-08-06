export type API = {
    state_create: {
        state: string
    },
    user_exists: {
        exists: boolean
    },
    session_create: {
        redirect: string
    }
}