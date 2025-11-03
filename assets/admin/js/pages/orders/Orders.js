import React, { useState } from "react";
import { CircularProgress, Typography } from "@mui/material";
import OrdersTable from "../../Components/Pages/Orders/OrdersTable";
import { useOrderProvider } from "../../context/OrderContext";

const Orders = () => {
  const { orders, loadingOrders } = useOrderProvider();

  const [orderBy, setOrderBy] = useState("date_created");
  const [orderDirection, setOrderDirection] = useState("desc");
  const [page, setPage] = useState(0);
  const [rowsPerPage, setRowsPerPage] = useState(10);

  const handleSort = (property) => {
    const isAsc = orderBy === property && orderDirection === "asc";
    setOrderDirection(isAsc ? "desc" : "asc");
    setOrderBy(property);
  };

  if (loadingOrders) {
    return (
      <div style={{ textAlign: "center", padding: "2rem" }}>
        <CircularProgress />
        <Typography variant="body1" sx={{ mt: 2 }}>
          Loading orders...
        </Typography>
      </div>
    );
  }

  return (
    <OrdersTable
      orders={orders}
      orderBy={orderBy}
      orderDirection={orderDirection}
      handleSort={handleSort}
      page={page}
      rowsPerPage={rowsPerPage}
      handleChangePage={(e, newPage) => setPage(newPage)}
      handleChangeRowsPerPage={(e) => {
        setRowsPerPage(parseInt(e.target.value, 10));
        setPage(0);
      }}
    />
  );
};

export default Orders;
