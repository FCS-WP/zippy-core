import React, { useState } from "react";
import MinCostFreeShip from "./MinCostFreeShip";
import { Box, Collapse, Divider, FormControlLabel, Switch } from "@mui/material";
import ShippingTable from "./ShippingTable";

const ShippingContent = () => {
  const [tableChecked, setTableChecked] = useState(false);

  const handleChangeTableChecked = () => {
    setTableChecked((prev) => !prev);
  };

  return (
    <Box className="zippy-settings-content">
      <Box px={5}>
        <MinCostFreeShip />
        <Divider sx={{ my: 4 }} />
        <Box>
          <FormControlLabel
            sx={{ mb: 2 }}
            control={
              <Switch
                checked={tableChecked}
                onChange={handleChangeTableChecked}
                name="active-table"
              />
            }
            label="Shipping By Categories"
          />
            <Collapse in={tableChecked}>
              <ShippingTable />
            </Collapse>
        </Box>
      </Box>
    </Box>
  );
};

export default ShippingContent;
