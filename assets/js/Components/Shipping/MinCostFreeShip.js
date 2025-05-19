import { FormControl, Grid, InputAdornment, InputLabel, OutlinedInput } from "@mui/material";
import React from "react";

const MinCostFreeShip = () => {
  return (
    <div>
      <Grid container justifyContent={'center'}>
        <Grid size={2}>Min cost for free delivery: </Grid>
        <Grid size={6}>
         <FormControl>
          <InputLabel htmlFor="outlined-adornment-amount">Amount</InputLabel>
          <OutlinedInput
            size="medium"
            type="number"
            id="outlined-adornment-amount"
            startAdornment={<InputAdornment position="start">$</InputAdornment>}
            label="Amount"
          />
        </FormControl>
        </Grid>
      </Grid>
    </div>
  );
};

export default MinCostFreeShip;
