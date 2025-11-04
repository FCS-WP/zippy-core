import React, { createContext, useContext, useState, useEffect } from "react";
import { Api } from "../api/admin";

const OrderContext = createContext();

export const OrderProvider = ({ children }) => {
  const [orders, setOrders] = useState([]);
  const [loadingOrders, setLoadingOrders] = useState(true);

  //Filter
  const [fromDate, setFromDate] = useState("");
  const [toDate, setToDate] = useState("");

  const fetchOrders = async (filters) => {
    try {
      setLoadingOrders(true);
      const res = await Api.getOrders(filters);
      if (res.data.status === "success") setOrders(res.data.orders);
    } catch (err) {
      console.error("Error fetching orders:", err);
    } finally {
      setLoadingOrders(false);
    }
  };

  useEffect(() => {
    fetchOrders();
  }, []);

  const handleFilterOrder = (filters) => {
    fetchOrders(filters);
  };

  const value = {
    orders,
    loadingOrders,
    fromDate,
    toDate,
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
