import {
  FormControl,
  Grid,
  InputAdornment,
  InputLabel,
  OutlinedInput,
  Table,
  Typography,
} from "@mui/material";
import React, { useEffect, useState } from "react";
import { useShippingProvider } from "../../contexts/ShippingProvider";

const MinCostFreeShip = ({ onChangeCost }) => {
  const { minCost } = useShippingProvider();
  const [costValue, setCostValue] = useState(minCost);

  const handleChangeCost = (e) => {
   if (e.target.value < 0) {
    return;
   }
   setCostValue(e.target.value);
  }

  useEffect(()=>{
    setCostValue(minCost);
  }, [minCost])
  
  useEffect(()=>{
   onChangeCost(costValue);
  }, [costValue])

  return (
    <div>
      <Grid container>
        <Grid size={2}>
          <Typography variant="body2" fontSize={16} fontWeight={600}>
            Min cost for free delivery:{" "}
          </Typography>
        </Grid>
        <Grid size={6}>
          <FormControl>
            <InputLabel htmlFor="outlined-adornment-amount">Amount</InputLabel>
            <OutlinedInput
              size="small"
              type="number"
              value={costValue ?? ""}
              onChange={handleChangeCost}
              id="outlined-adornment-amount"
              startAdornment={
                <InputAdornment position="start">$</InputAdornment>
              }
              label="Amount"
            />
          </FormControl>
        </Grid>
      </Grid>
    </div>
  );
};

export default MinCostFreeShip;
