import React, { createContext, useContext, useState, useEffect } from "react";
import { Api } from "../api/admin";

const OrderContext = createContext();

export const OrderProvider = ({ children }) => {
  const [orders, setOrders] = useState([]);
  const [page, setPage] = useState(0);
  const [totalOrders, setTotalOrders] = useState(0);
  const [loadingOrders, setLoadingOrders] = useState(true);
  const [status, setStatus] = useState("");

  //Filter
  const [fromDate, setFromDate] = useState("");
  const [toDate, setToDate] = useState("");

  const fetchOrders = async (filters) => {
    try {
      setLoadingOrders(true);
      const params = { ...filters, page: page + 1, per_page: 10 };
      const res = await Api.getOrders(params);
      if (res.data.status === "success") {
        setOrders(res.data.orders);
        setTotalOrders(res.data.total_orders);
      }
    } catch (err) {
      console.error("Error fetching orders:", err);
    } finally {
      setLoadingOrders(false);
    }
  };

  useEffect(() => {
    fetchOrders();
  }, [page]);

  const handleFilterOrder = (filters) => {
    fetchOrders(filters);
  };

  const value = {
    orders,
    totalOrders,
    loadingOrders,
    fromDate,
    toDate,
    page,
    status,
    setStatus,
    setPage,
    setOrders,
    setFromDate,
    setToDate,
    handleFilterOrder,
    fetchOrders,
  };

  return (
    <OrderContext.Provider value={value}>{children}</OrderContext.Provider>
  );
};

export const useOrderProvider = () => useContext(OrderContext);
