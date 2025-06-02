import React, { useEffect, useState } from "react";
import MinCostFreeShip from "./MinCostFreeShip";
import {
  Box,
  Collapse,
  Divider,
  FormControlLabel,
  Switch,
} from "@mui/material";
import ShippingTable from "./ShippingTable";
import { useShippingProvider } from "../../contexts/ShippingProvider";

const ShippingContent = ({ onUpdateData }) => {
  const { isShipByCategoryActive } = useShippingProvider();
  const { minCost } = useShippingProvider();
  const [cost, setCost] = useState(minCost);
  const [tableChecked, setTableChecked] = useState(isShipByCategoryActive);

  const handleChangeTableChecked = () => {
    setTableChecked((prev) => !prev);
  };

  const handleChangeCost = (newValue) => {
    setCost(newValue);
  };

  useEffect(() => {
    setTableChecked(isShipByCategoryActive);
  }, [isShipByCategoryActive]);

  useEffect(() => {
    onUpdateData({
      cost: cost,
      active: tableChecked,
    });

    return () => {};
  }, [cost, tableChecked]);

  return (
    <Box className="zippy-settings-content">
      <Box px={5}>
        <MinCostFreeShip onChangeCost={handleChangeCost} />
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
