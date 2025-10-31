import { makeRequest } from "../../axios";

export const OrderApi = {
  async getOrders(params) {
    return await makeRequest("/orders", params);
  },
};
