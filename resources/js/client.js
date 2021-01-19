import axios from 'axios';

export default {
    secureClient() {
        var config = {
            baseURL: '/api',
            headers: { Authorization: (localStorage.getItem('token_type') || null) + " " + (localStorage.getItem('access_token') || null), Accept: "application/json" }
        };
        return axios.create(config);
    },
    client() {
        return axios.create({
            baseURL: '/api'
        });
    },
    all(url, params) {
        return this.secureClient().get(url, params);
    },
    find(url, id) {
        return this.secureClient().get(`${url}/${id}`);
    },
    create(url, data) {
        return this.secureClient().post(url, data);
    },
    update(url, id, data) {
        return this.secureClient().patch(`${url}/${id}`, data);
    },
    delete(url, id) {
        return this.secureClient().delete(`${url}/${id}`);
    },
};