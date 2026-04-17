import React from "react";
import { Box, Typography } from "@mui/material";

const OrderSummary = ({
  subtotal,
  gst,
  shippingTotal,
  feesTotal,
  couponsTotal,
  total,
}) => (
  <Box
    sx={{
      p: 2,
      display: "flex",
      flexDirection: "column",
      alignItems: "flex-end",
      gap: 1,
    }}
  >
    <Typography sx={{ fontWeight: "bold", fontSize: "0.85rem" }}>
      Subtotal:{" "}
      <Typography component="span" sx={{ fontWeight: "normal" }}>
        ${Number(subtotal || 0).toFixed(2)}
      </Typography>
    </Typography>

    {shippingTotal > 0 && (
      <Typography sx={{ fontWeight: "bold", fontSize: "0.85rem" }}>
        Shipping:{" "}
        <Typography component="span" sx={{ fontWeight: "normal" }}>
          ${Number(shippingTotal || 0).toFixed(2)}
        </Typography>
      </Typography>
    )}
    {feesTotal > 0 && (
      <Typography sx={{ fontWeight: "bold", fontSize: "0.85rem" }}>
        Extra Fee:{" "}
        <Typography component="span" sx={{ fontWeight: "normal" }}>
          ${Number(feesTotal || 0).toFixed(2)}
        </Typography>
      </Typography>
    )}
    {couponsTotal > 0 && (
      <Typography sx={{ fontWeight: "bold", fontSize: "0.85rem" }}>
        Coupons:{" "}
        <Typography component="span" sx={{ fontWeight: "normal" }}>
          -${Number(couponsTotal || 0).toFixed(2)}
        </Typography>
      </Typography>
    )}
    <Typography sx={{ fontWeight: "bold", fontSize: "0.85rem" }}>
      GST:{" "}
      <Typography component="span" sx={{ fontWeight: "normal" }}>
        ${Number(gst || 0).toFixed(2)}
      </Typography>
    </Typography>
    <Typography variant="h6" sx={{ fontWeight: "bold", fontSize: "1rem" }}>
      Total: ${Number(total || 0).toFixed(2)}
    </Typography>
  </Box>
);

export default OrderSummary;
