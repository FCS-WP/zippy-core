import { makeRequest } from "../axios";

makeRequest;
export const Woocommerce = {
  async getTotalSales(params) {
    return await makeRequest("/wc/v3/reports/sales", params);
  },
  async getCategoriesSale(params){
    return await makeRequest("/wc-analytics/reports/categories", params);
  },
  async getCategories(params){
    return await makeRequest("/wc-analytics/products/categories", params);
  },
  async getOrderData(params){
    return await makeRequest("/wc-analytics/reports/products/stats", params);
  }
};
