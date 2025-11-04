import React, { useEffect, useState } from "react";
import { Button, TextField, Stack, FormControl } from "@mui/material";
import FilterDateRange from "./FilterDateRange";
import { useOrderProvider } from "../../../context/OrderContext";

const formatDate = (dateStr) => {
  if (!dateStr) return "";
  const [y, m, d] = dateStr.split("-");
  return `${y}-${m}-${d}`;
};

const FilterOrder = ({ fromDate, setFromDate, toDate, setToDate }) => {
  const { handleFilterOrder } = useOrderProvider();

  const onFilter = () => {
    handleFilterOrder({
      date_from: formatDate(fromDate),
      date_to: formatDate(toDate),
    });
  };

  return (
    <Stack direction="row" spacing={1} alignItems="center">
      <FilterDateRange
        fromDate={fromDate}
        setFromDate={setFromDate}
        toDate={toDate}
        setToDate={setToDate}
      />
      <Button
        variant="outlined"
        sx={{
          height: "32px",
          fontSize: "12px",
          borderRadius: "2px",
          background: "#f6f7f7",
          color: "#2271b1",
          border: "1px solid #2271b1",
          boxShadow: "none",
          "&:hover": { background: "#e1e4e6", boxShadow: "none" },
          "@media (max-width: 600px)": {
            height: "40px",
            fontSize: "10px",
          },
        }}
        onClick={onFilter}
      >
        Filter
      </Button>
    </Stack>
  );
};

export default FilterOrder;
