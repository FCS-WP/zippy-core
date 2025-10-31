import React, { useState, useEffect } from "react";
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
  Typography,
  Link,
  Checkbox,
} from "@mui/material";
import OrderStatusLabel from "./OrderStatusLabel";
import BillingCell from "./BillingCell";
import BulkAction from "./BulkAction";

const OrdersTable = ({
  orders,
  orderBy,
  orderDirection,
  handleSort,
  page,
  rowsPerPage,
  handleChangePage,
  handleChangeRowsPerPage,
}) => {
  const [selectedOrders, setSelectedOrders] = useState([]);
  const [paginatedOrders, setPaginatedOrders] = useState([]);

  useEffect(() => {
    const sorted = [...orders].sort((a, b) => {
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

    setPaginatedOrders(
      sorted.slice(page * rowsPerPage, page * rowsPerPage + rowsPerPage)
    );
  }, [orders, orderBy, orderDirection, page, rowsPerPage]);

  const isAllChecked = () =>
    paginatedOrders.length > 0 &&
    paginatedOrders.every((o) => selectedOrders.includes(String(o.id)));

  const handleChange = (orderId) => {
    setSelectedOrders((prev) =>
      prev.includes(String(orderId))
        ? prev.filter((x) => x !== String(orderId))
        : [...prev, String(orderId)]
    );
  };

  const handleSelectAll = (e) => {
    e.stopPropagation();
    const allIds = paginatedOrders.map((o) => String(o.id));
    const isAllSelected = allIds.every((id) => selectedOrders.includes(id));

    if (isAllSelected) {
      setSelectedOrders((prev) => prev.filter((id) => !allIds.includes(id)));
    } else {
      setSelectedOrders((prev) => [...new Set([...prev, ...allIds])]);
    }
  };

  return (
    <Paper sx={{ p: 2 }}>
      <Typography variant="h6" sx={{ mb: 2 }}>
        Orders
      </Typography>
      <BulkAction
        selectedOrders={selectedOrders}
        setOrders={setPaginatedOrders}
      />
      <TableContainer sx={{ my: "20px" }}>
        <Table>
          <TableHead>
            <TableRow>
              <TableCell
                padding="checkbox"
                sx={{
                  color: "#333",
                  backgroundColor: "#f5f5f5",
                }}
              >
                <Checkbox
                  checked={isAllChecked()}
                  indeterminate={selectedOrders.length > 0 && !isAllChecked()}
                  onClick={handleSelectAll}
                />
              </TableCell>
              {[
                { id: "order_number", label: "Order #" },
                { id: "phone_number", label: "Phone Number" },
                { id: "status", label: "Status" },
                { id: "total", label: "Total (VND)" },
                { id: "payment_method", label: "Payment Method" },
                { id: "shipping_info", label: "Shipping Info" },
                { id: "date_created", label: "Date Created" },
              ].map((col) => (
                <TableCell
                  key={col.id}
                  sortDirection={orderBy === col.id ? orderDirection : false}
                  sx={{
                    fontWeight: "bold",
                    color: "#333",
                    backgroundColor: "#f5f5f5",
                  }}
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
              <TableRow key={order.id} id={`order-${order.id}`}>
                <TableCell padding="checkbox">
                  <Checkbox
                    name="id[]"
                    value={order.id}
                    checked={selectedOrders.includes(String(order.id))}
                    onClick={() => handleChange(order.id)}
                  />
                </TableCell>
                <TableCell>
                  <Link
                    href={`/wp-admin/admin.php?page=wc-orders&action=edit&id=${order.id}`}
                    underline="hover"
                    color="primary"
                    sx={{ fontWeight: "bold", cursor: "pointer" }}
                  >
                    #{order.order_number} - {order.billing?.first_name}{" "}
                    {order.billing?.last_name}
                  </Link>
                </TableCell>
                <BillingCell billing={order.billing} />
                <TableCell>
                  <OrderStatusLabel status={order.status} />
                </TableCell>
                <TableCell>
                  {parseInt(order.total).toLocaleString()} {order.currency}
                </TableCell>
                <TableCell>{order.payment_method?.title || "N/A"}</TableCell>
                <TableCell sx={{ fontSize: "0.85rem", color: "#555" }}>
                  {order.billing?.first_name} {order.billing?.last_name}
                  {order.shipping?.company && `, ${order.shipping.company}`}
                  <br />
                  <a
                    href={`https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(
                      `${order.shipping?.address_1 || ""} ${
                        order.shipping?.address_2 || ""
                      }, ${order.shipping?.city || ""}`
                    )}`}
                    target="_blank"
                    rel="noopener noreferrer"
                    style={{
                      textDecoration: "none",
                      color: "#1976d2",
                      fontSize: "0.8rem",
                      display: "inline-block",
                      marginTop: "2px",
                    }}
                  >
                    {order.shipping?.address_1} {order.shipping?.address_2},{" "}
                    {order.shipping?.city}
                  </a>
                </TableCell>
                <TableCell>{order.date_created}</TableCell>
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

export default OrdersTable;
