import { makeRequest } from "../axios";

export const Api = {
  async checkKeyExits(params) {
    return await makeRequest("/check_option", params);
  },
  async updateSettings(params) {
    return await makeRequest("/update_settings", params, "POST");
  },
  async getShippingConfigs(params) {
    return await makeRequest("/shipping", params);
  },
  async updateShippingConfigs(params) {
    return await makeRequest("/shipping", params, "PUT");
  },
  async deleteShippingConfigs(params) {
    return await makeRequest("/shipping", params, "DELETE");
  },
  async saveShippingConfigs(params) {
    return await makeRequest("/save-shipping-config", params, "POST");
  },
  async searchCategories(params) {
    return await makeRequest("/categories", params);
  },
};
