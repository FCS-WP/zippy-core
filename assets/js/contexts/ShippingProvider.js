import React, { useContext, useEffect, useState } from "react";
import ShippingContext from "./ShippingContext";

export const ShippingProvider = ({ children }) => {

  useEffect(() => {
    return () => {};
  }, []);

  const value = {};

  return <ShippingContext.Provider value={value}>{children}</ShippingContext.Provider>;
};

export const useShippingProvider = () => useContext(ShippingContext);
