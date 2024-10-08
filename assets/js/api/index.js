import { makeRequest } from "../axios";

export const Api = {
  async checkKeyExits(params) {
    return await makeRequest("/check_option", params);
  },
  async updateSetting(params) {
    return await makeRequest("/update_settings", params, "POST");
  },
};
