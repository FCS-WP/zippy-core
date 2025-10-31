import React, { createContext, useContext, useState, useEffect } from "react";
import { Api } from "../api/admin";

const OrderContext = createContext();

export const OrderProvider = ({ children }) => {
  const [orders, setOrders] = useState([]);
  const [loading, setLoading] = useState(true);

  const [fromDate, setFromDate] = useState("");
  const [toDate, setToDate] = useState("");

  const fetchOrders = async (from = "", to = "") => {
    try {
      setLoading(true);
      const res = await Api.getOrders({
        date_from: from,
        date_to: to,
      });
      if (res.data.success) setOrders(res.data.result.orders);
    } catch (err) {
      console.error("Error fetching orders:", err);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchOrders();
  }, []);

  const handleFilterDateRange = ({ date_from, date_to }) => {
    setFromDate(date_from);
    setToDate(date_to);
    fetchOrders(date_from, date_to);
  };

  const value = {
    orders,
    loading,
    fromDate,
    toDate,
    setFromDate,
    setToDate,
    handleFilterDateRange,
    fetchOrders,
  };

  return (
    <OrderContext.Provider value={value}>{children}</OrderContext.Provider>
  );
};

export const useOrderProvider = () => useContext(OrderContext);
