import React, { useEffect, useState } from "react";
import {
  Table,
  TableBody,
  TableCell,
  TableContainer,
  TableHead,
  TableRow,
  TableSortLabel,
  TablePagination,
  Paper,
  CircularProgress,
  Typography,
} from "@mui/material";

const Orders = () => {
  const [orders, setOrders] = useState([]);
  const [orderBy, setOrderBy] = useState("date_created");
  const [orderDirection, setOrderDirection] = useState("desc");
  const [page, setPage] = useState(0);
  const [rowsPerPage, setRowsPerPage] = useState(10);
  const [loading, setLoading] = useState(true);

  // Fetch orders from API
  useEffect(() => {
    const fetchOrders = async () => {
      try {
        const res = await fetch(
          "http://localhost:12381/wp-json/zippy-core/v2/orders"
        );
        const data = await res.json();
        if (data.success) setOrders(data.result.orders);
      } catch (err) {
        console.error("Error fetching orders:", err);
      } finally {
        setLoading(false);
      }
    };
    fetchOrders();
  }, []);

  // Handle sorting
  const handleSort = (property) => {
    const isAsc = orderBy === property && orderDirection === "asc";
    setOrderDirection(isAsc ? "desc" : "asc");
    setOrderBy(property);
  };

  const sortedOrders = [...orders].sort((a, b) => {
    let valA = a[orderBy];
    let valB = b[orderBy];
    if (orderBy === "total") {
      valA = parseFloat(valA);
      valB = parseFloat(valB);
    }
    if (valA < valB) return orderDirection === "asc" ? -1 : 1;
    if (valA > valB) return orderDirection === "asc" ? 1 : -1;
    return 0;
  });

  // Pagination
  const handleChangePage = (event, newPage) => setPage(newPage);
  const handleChangeRowsPerPage = (event) => {
    setRowsPerPage(parseInt(event.target.value, 10));
    setPage(0);
  };

  const paginatedOrders = sortedOrders.slice(
    page * rowsPerPage,
    page * rowsPerPage + rowsPerPage
  );

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
    <Paper sx={{ p: 2 }}>
      <Typography variant="h6" sx={{ mb: 2 }}>
        Orders List
      </Typography>
      <TableContainer>
        <Table>
          <TableHead>
            <TableRow>
              {[
                { id: "order_number", label: "Order #" },
                { id: "date_created", label: "Date Created" },
                { id: "status", label: "Status" },
                { id: "total", label: "Total (VND)" },
                { id: "payment_method", label: "Payment Method" },
              ].map((col) => (
                <TableCell
                  key={col.id}
                  sortDirection={orderBy === col.id ? orderDirection : false}
                >
                  <TableSortLabel
                    active={orderBy === col.id}
                    direction={orderBy === col.id ? orderDirection : "asc"}
                    onClick={() => handleSort(col.id)}
                  >
                    {col.label}
                  </TableSortLabel>
                </TableCell>
              ))}
            </TableRow>
          </TableHead>

          <TableBody>
            {paginatedOrders.map((order) => (
              <TableRow key={order.id}>
                <TableCell>{order.order_number}</TableCell>
                <TableCell>{order.date_created}</TableCell>
                <TableCell>{order.status}</TableCell>
                <TableCell>
                  {parseInt(order.total).toLocaleString()} {order.currency}
                </TableCell>
                <TableCell>{order.payment_method?.title || "-"}</TableCell>
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </TableContainer>

      <TablePagination
        component="div"
        count={orders.length}
        page={page}
        onPageChange={handleChangePage}
        rowsPerPage={rowsPerPage}
        onRowsPerPageChange={handleChangeRowsPerPage}
        rowsPerPageOptions={[5, 10, 20]}
      />
    </Paper>
  );
};

export default Orders;
