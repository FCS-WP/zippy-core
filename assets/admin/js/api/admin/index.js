import { makeRequest } from "../axios";

export const Api = {
  async getOrders(params) {
    return await makeRequest("/orders", params);
  },
  async updateOrderStatus(params) {
    return await makeRequest("/update-order-status", params, "POST");
  },
  async moveToTrashOrder(params) {
    return await makeRequest("/move-to-trash", params, "POST");
  },
};
