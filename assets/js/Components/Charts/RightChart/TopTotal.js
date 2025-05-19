import React, { useEffect, useState, useCallback } from "react";

import { Woocommerce } from "../../../Woocommerce/woocommerce";
import { Card, CardContent, Grid } from "@mui/material";
const TopTotal = ({ params, ...props }) => {
  const [orderTotal, setOrderTotal] = useState(0);
  const [productSold, setProductSold] = useState(0);

  const fetchData = useCallback(async (params) => {
    const { data } = await Woocommerce.getOrderData(params);
    const dataTotal = data.totals;
    setOrderTotal(dataTotal.orders_count || 0);
    setProductSold(dataTotal.items_sold || 0);
  }, []);

  useEffect(() => {
    fetchData(params);
  }, [params]);

  return (
    <>
      <Grid size={3} >
        <Card className="mt-0">
          <CardContent>
            <label>Orders</label>
            <h5>{orderTotal}</h5>
          </CardContent>
        </Card>
      </Grid>
      <Grid size={3}>
        <Card className="mt-0">
          <CardContent>
            <label>Products Sold</label>
            <h5>{productSold}</h5>
          </CardContent>
        </Card>
      </Grid>
    </>
  );
};
export default TopTotal;
