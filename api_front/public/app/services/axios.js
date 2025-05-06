import axios from "axios";

const instance = axios.create({
    baseURL: process.env.API_BASE_URL + '/api',
    responseType: "json"
})

instance.defaults.withCredentials = true;
instance.defaults.headers.common['Content-Type'] = "application/json";
instance.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

export default instance;
