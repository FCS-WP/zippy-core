import React from "react";
import { Chip } from "@mui/material";

const statusColors = {
  processing: { label: "Processing", color: "warning" },
  completed: { label: "Completed", color: "success" },
  pending: { label: "Pending", color: "info" },
  cancelled: { label: "Cancelled", color: "error" },
  refunded: { label: "Refunded", color: "secondary" },
};

export default function OrderStatusLabel({ status }) {
  const normalized = status?.toLowerCase() || "";
  const info = statusColors[normalized] || { label: status, color: "default" };

  return (
    <Chip
      label={info.label}
      color={info.color}
      variant="outlined"
      size="small"
      sx={{ fontWeight: "bold", textTransform: "capitalize" }}
    />
  );
}
