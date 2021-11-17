import Vue from 'vue';
import Vuex from 'vuex';
import axios from 'axios';

Vue.use(Vuex);

const baseUrl = '/';
export const store = new Vuex.Store({
    state: {
        all_docs:[],
        all_docs_detail:[],
        my_docs: [],
        doc_type: [],
        doc_group: [],
        doc_status: [],
        user_access: [],
        permissionList: [],
        no_permissions: []
    },
    actions: {
        /* Get all the document types and groups*/
        async getDocTypeGroup({ commit }) {
            axios.get('/type-group-status')
                .then(response => {
                    commit('GET_TYPE_GROUP_STATUS', response.data)
                })
        },
        /* Get the logged in user document list */
        async getMyDocs({ commit }, id) {
            axios.get('/get-my-docs/' + id)
                .then(response => {
                    commit('GET_MY_DOCS', response.data)
                })
                .catch(error => {
                    commit('GET_MY_DOCS', error)
                })
        },
        async getUserAccess({ commit }) {
            axios.get('/user-permissions').then(response => {
                commit('GET_USER_ACCESS', response.data);
            }).catch(error => {
                commit('GET_USER_ACCESS', error);
            });
        }
    },
    mutations: {
        GET_USER_ACCESS(state, response) {
            state.user_access = response;
        },
        GET_TYPE_GROUP_STATUS(state, response) {
            state.doc_type = response.doc_type;
            state.doc_group = response.doc_group;
            state.doc_status = response.doc_status;
            state.permissionList = response.permissions;
            state.no_permissions = response.no_permissions;
        },
        GET_MY_DOCS(state, payload) {
            state.doc_group = payload.doc_group;
            state.my_docs = payload.my_docs;
            state.all_docs = payload.all_docs;
            state.all_docs_detail = payload.all_docs_detail;
        },
        UPDATE_TYPE(state, response) {
            let index = state.doc_type.findIndex(data => data.id == response.id);

            state.doc_type[index].type_code = response.type_code;
        }
    }
})