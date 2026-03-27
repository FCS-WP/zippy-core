import React, { useEffect, useState } from "react";
import { Box, TextField, Typography } from "@mui/material";
import { Api } from "../../../api/admin";

const PreOrderOptions = ({ orderID }) => {
  const [enablePreOrder, setEnablePreOrder] = useState("");

  const fetchPreOrderOption = async () => {
    const { data } = await Api.getPreOrderOptions({ order_id: orderID });
    if (data.status === "success") {
      setEnablePreOrder(data.is_pre_order || "");
    }
  };

  useEffect(() => {
    if (orderID) {
      fetchPreOrderOption();
    }
  }, [orderID]);

  return (
    <div className="form-field form-field-wide">
      <Box>
        <Typography variant="body2" sx={{ mt: 1, fontSize: "13px" }}>
          Pre-Order Option
        </Typography>

        <TextField
          select
          SelectProps={{ native: true }}
          value={enablePreOrder}
          onChange={(e) => setEnablePreOrder(e.target.value)}
          fullWidth
        >
          <option value="">Select</option>
          <option value="yes">Yes</option>
          <option value="no">No</option>
        </TextField>

        <input type="hidden" name="enable_pre_orders" value={enablePreOrder} />
      </Box>
    </div>
  );
};

export default PreOrderOptions;
