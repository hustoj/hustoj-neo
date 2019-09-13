import Vue from "vue";

Vue.filter('showStatusBtn', function(status) {
    if(status == 1) {
        return 'Enable';
    }
    return 'Disable';
});

Vue.filter('privateStatus', function(status) {
    if(status) {
        return 'Private';
    }
    return 'Public';
});

export const showStatusBtn = Vue.filter('showStatusBtn');
export const privateStatus = Vue.filter('privateStatus');
