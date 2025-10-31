import { IconButton, TableCell } from "@mui/material";
import VisibilityIcon from "@mui/icons-material/Visibility";
import React, { useState } from "react";
import ShippingModal from "./ShippingModal";

const ShippingCell = ({ shipping, paymentMethod }) => {
  const [open, setOpen] = useState(false);

  return (
    <>
      <TableCell>
        <IconButton color="primary" onClick={() => setOpen(true)}>
          <VisibilityIcon />
        </IconButton>
        {paymentMethod?.title || "-"}
      </TableCell>

      <ShippingModal
        open={open}
        onClose={() => setOpen(false)}
        shipping={shipping}
      />
    </>
  );
};

export default ShippingCell;
