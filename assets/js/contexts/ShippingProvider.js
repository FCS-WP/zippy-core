import React, { useContext, useEffect, useState } from "react";
import ShippingContext from "./ShippingContext";
import { exampleData } from "../Components/Shipping/exampleData";

export const ShippingProvider = ({ children }) => {
  const [shippingData, setShippingData] = useState(null);
  const [minCost, setMinCost] = useState(0);

  const getShippingConfigs = async () => {
    // Call Api here;
    const initShippingData = {
      min_cost: 50,
      ship_by_categories: exampleData,
    }
    setMinCost(initShippingData.min_cost)
    setShippingData(initShippingData.ship_by_categories);
  }

  useEffect(() => {
    getShippingConfigs();
    return () => {};
  }, []);

  const value = {
    shippingData,
    minCost,
  };

  return <ShippingContext.Provider value={value}>{children}</ShippingContext.Provider>;
};

export const useShippingProvider = () => useContext(ShippingContext);
