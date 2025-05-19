import React from "react";
import { ShippingProvider } from "../../../contexts/ShippingProvider";
import ShippingContent from "../../Shipping/ShippingContent";
const CustomizeShipping = () => {
  return (
    <ShippingProvider>
      <ShippingContent />
    </ShippingProvider>
  );
};

export default CustomizeShipping;
