import React, { useContext, useEffect, useState } from "react";
import ShippingContext from "./ShippingContext";
import { exampleData } from "../Components/Shipping/exampleData";
import { Api } from "../api";

export const ShippingProvider = ({ children }) => {
  const [shippingData, setShippingData] = useState(null);
  const [minCost, setMinCost] = useState(0);
  const [isShipByCategoryActive, setIsShipByCategoryActive] = useState(false);

  const fetchShippingConfigs = async () => {
    const response = await Api.getShippingConfigs();

    if (!response || response.data.success !== true) {
      console.log("Failed to get shipping configs");
      return;
    }
    const data = response.data;

    setMinCost(data.min_cost ?? 0);
    setIsShipByCategoryActive(data.is_active == 1 ? true : false);
    setShippingData(data.results ?? []);

    return;
  };

  useEffect(() => {
    fetchShippingConfigs();
    return () => {};
  }, []);

  const value = {
    shippingData,
    minCost,
    isShipByCategoryActive,
    fetchShippingConfigs,
  };

  return (
    <ShippingContext.Provider value={value}>
      {children}
    </ShippingContext.Provider>
  );
};

export const useShippingProvider = () => useContext(ShippingContext);
