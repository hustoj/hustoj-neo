
export default {
    store:{
        problems:[]
    },
    mutations: {
        setProblems(state, data) {
            console.log(data);
        }
    },
    actions: {
        paginate(content) {
            let loading = Vue.prototype.$loading({text:"玩命加载中..."});

            Vue.prototype.$http.get("/admin/problems", {})
                .then(function(response){
                    loading.close();
                    content.commit('setProblems', response.data);
                });
        },
    },
}