import React from "react";
import { OrderProvider } from "../../context/OrderContext";
import Orders from "./Orders";

function App() {
  return (
    <OrderProvider>
      <Orders />
    </OrderProvider>
  );
}

export default App;
