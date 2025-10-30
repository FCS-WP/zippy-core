import { makeRequest } from "./axios";
export const Api = {
  async getOrders(params) {
    return await makeRequest("/check_option", params);
  },
};
