import { defineStore } from 'pinia';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    accessToken: null,
  }),
  actions: {
    setAuth(data) {
      this.user = data.user;
      this.accessToken = data.access_token;
    },
    logout() {
      this.user = null;
      this.accessToken = null;
    },
  },
  persist: {
    storage: localStorage,
  },
});