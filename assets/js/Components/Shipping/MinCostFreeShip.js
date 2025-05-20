import {
  FormControl,
  Grid,
  InputAdornment,
  InputLabel,
  OutlinedInput,
  Table,
  Typography,
} from "@mui/material";
import React from "react";

const MinCostFreeShip = () => {
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
