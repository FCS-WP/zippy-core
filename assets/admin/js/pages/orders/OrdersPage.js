import React from "react";
import { OrderProvider } from "../../context/OrderContext";
import Orders from "./Orders";
import { ToastContainer } from "react-toastify";

function OrdersPage() {
  return (
    <OrderProvider>
      <Orders />
      <ToastContainer />
    </OrderProvider>
  );
}

export default OrdersPage;
