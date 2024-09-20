import axios from "axios";

export const makeRequest = async (endpoint, params = {}, method = "GET") => {
  const baseURL = "/wp-json";
  const consumer_key = "ck_cc7d6a3636d9bb3739710d1afec4ed1fb5dce20b";
  const consumer_secret = "cs_81e2dec732c1befa846a740525fefa04ea92d4c2";

  const api = axios.create({
    baseURL: baseURL,
    auth: {
      username: consumer_key,
      password: consumer_secret,
    },
  });
  let res = null;

  const config = {
    url: endpoint,
    params: params,
    method: method,
  };
  try {
    let res = null;

    res = await api.request(config);
    const data = res.data;
    return { data };
  } catch {
    (error) => {
      if (!error?.response) {
        console.error("❗Error", error.message);
        return { ...error, catchedError: error };
      }

      console.error(error.response.statusText);
      return error;
    };
  }
};
