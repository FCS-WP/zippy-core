import React from "react";
import { ShippingProvider } from "../../../contexts/ShippingProvider";
import ShippingContent from "../../Shipping/ShippingContent";
const CustomizeShipping = ({ onUpdateData }) => {
  return (
    <ShippingProvider>
      <ShippingContent onUpdateData={onUpdateData} />
    </ShippingProvider>
  );
};

export default CustomizeShipping;
