import { makeRequest } from "../axios";

export const Api = {
  async checkAuthentication(params) {
    return await makeRequest("/auth_status", params);
  },
};
