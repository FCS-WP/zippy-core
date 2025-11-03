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
  async getOrderInfo(params) {
    return await makeRequest("/get-order-info", params);
  },
  async removeOrderItem(params) {
    return await makeRequest("/remove-item-order", params, "POST");
  },
  async updateOrderItemMetaData(orderId, action, params) {
    return await makeRequest(
      `/update-meta-data-order-item?order_id=${orderId}&action=${action}`,
      params,
      "POST"
    );
  },
  async applyCouponToOrder(params) {
    return await makeRequest("/apply_coupon_to_order", params, "POST");
  },
  async updateOrderStatus(params) {
    return await makeRequest("/update-order-status", params, "POST");
  },
  async moveToTrashOrder(params) {
    return await makeRequest("/move-to-trash", params, "POST");
  },
  async getCustomers(params) {
    return await makeRequest("/get-list-customers", params);
  },
  async updatePriceProductByUser(params) {
    return await makeRequest("/update-price-product-by-user", params);
  },
  async getAdminNameFromOrder(params) {
    return await makeRequest("/admin-name-from-order", params);
  },
};
