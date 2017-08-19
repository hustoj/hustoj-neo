export function fetch(url, params) {
    return new Promise((resolve, reject) => {
        this.$http.post(url, params)
            .then(response => {
                resolve(response.data);
            })
            .catch((error) => {
                reject(error);
            })
    })
}