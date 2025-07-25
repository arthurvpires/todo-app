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
      console.log('Auth state after set:', { user: this.user, accessToken: this.accessToken });
      console.log('LocalStorage after set:', localStorage.getItem('pinia/auth'));
    },
    logout() {
      this.user = null;
      this.accessToken = null;
      console.log('LocalStorage after logout:', localStorage.getItem('pinia/auth'));
    },
  },
  persist: {
    storage: localStorage,
  },
});