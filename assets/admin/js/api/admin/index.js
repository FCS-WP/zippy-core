import { OrderApi } from "./orders/index";
export const Api = {
  async getOrders(params) {
    return OrderApi.getOrders(params);
  },
};
