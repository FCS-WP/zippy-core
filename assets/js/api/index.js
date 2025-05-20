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
  async updateShippingConfig(params) {
    return await makeRequest("/shipping", params, "PUT");
  },
};
