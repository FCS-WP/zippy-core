import React, { useEffect, useState } from "react";
import { Button, Modal } from "react-bootstrap";

const ShippingConfigModal = ({data, show, onClose}) => {
  const [openModal, setOpenModal] = useState(show);
  const handleSubmitForm = () => {
    console.log("Submit form");
    onClose();
  }

  useEffect(()=>{
    setOpenModal(show);
  }, [show])
  return (
  <div>
    dialog
  </div>
  );
};

export default ShippingConfigModal;
