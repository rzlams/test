import axios from "axios";

const REST_ENDPOINT = "http://127.0.0.1:4444/soap/";
const AUTH_TOKEN_KEY = "authToken";

export async function httpRequest(data, action, method = "POST") {
    try {
        let res = await axios({
            url: REST_ENDPOINT + action,
            method: method,
            data: data
        });
        return res;
    }
    catch (err) {
      const message = err.response?.data?.message || err.message;
      const data = err.response?.data?.data || '';
      return { message, data, error: true };
    }
}

export function setAuthToken(token) {
    sessionStorage.setItem(AUTH_TOKEN_KEY, token);
}

export function getAuthToken() {
    return sessionStorage.getItem(AUTH_TOKEN_KEY);
}

export function clearAuthToken() {
    sessionStorage.removeItem(AUTH_TOKEN_KEY);
}
