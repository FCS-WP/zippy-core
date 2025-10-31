import React, { useEffect, useState } from "react";
import { CircularProgress, Typography } from "@mui/material";
import { Api } from "../../api/admin";
import OrdersTable from "../../Components/Pages/Orders/OrdersTable";

const Orders = () => {
  const [orders, setOrders] = useState([]);
  const [orderBy, setOrderBy] = useState("date_created");
  const [orderDirection, setOrderDirection] = useState("desc");
  const [page, setPage] = useState(0);
  const [rowsPerPage, setRowsPerPage] = useState(10);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchOrders = async () => {
      try {
        const res = await Api.getOrders();
        if (res.data.success) setOrders(res.data.result.orders);
      } catch (err) {
        console.error("Error fetching orders:", err);
      } finally {
        setLoading(false);
      }
    };
    fetchOrders();
  }, []);

  const handleSort = (property) => {
    const isAsc = orderBy === property && orderDirection === "asc";
    setOrderDirection(isAsc ? "desc" : "asc");
    setOrderBy(property);
  };

  const handleChangePage = (event, newPage) => setPage(newPage);
  const handleChangeRowsPerPage = (event) => {
    setRowsPerPage(parseInt(event.target.value, 10));
    setPage(0);
  };

  if (loading) {
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
      handleChangePage={handleChangePage}
      handleChangeRowsPerPage={handleChangeRowsPerPage}
    />
  );
};

export default Orders;
