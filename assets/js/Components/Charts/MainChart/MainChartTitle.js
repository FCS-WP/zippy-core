import { Box, Grid } from "@mui/material";
import React, { useEffect } from "react";
const MainChartTitle = ({
  netSales,
  totalSale,
  onClearDate,
  dateSelected,
  ...props
}) => {
  return (
    <Box>
      <Grid container>
        <Grid size={6}>
          <h4>Total Sales</h4>
        </Grid>
        <Grid size={{ xs: 6, sm: 3 }}>
          <label>Total Sales</label>
          <h5>${totalSale}</h5>
        </Grid>
        <Grid size={{ xs: 6, sm: 3 }}>
          <label>Net Sales</label>
          <h5>${netSales}</h5>
        </Grid>
      </Grid>
    </Box>
  );
};
export default MainChartTitle;
