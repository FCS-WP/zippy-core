import React, { useState } from "react";
import ShippingConfigModal from "./ShippingConfigModal";
import StaticExample from "./StaticExample";

const exampleCategories = [
  {
    id: 244,
    name: "Hat",
  },
  {
    id: 432,
    name: "High Heels",
  },
  {
    id: 23,
    name: "Shoes",
  },
];

const exampleData = [
  {
    id: 1,
    category_includes: [
      {
        id: 123,
        name: "Accessories",
      },
      {
        id: 432,
        name: "Bags",
      },
      {
        id: 111,
        name: "Wallets",
      },
    ],
    shipping_fee: 15,
  },
  {
    id: 3,
    category_includes: [
      {
        id: 123,
        name: "Accessories",
      },
      {
        id: 432,
        name: "Bags",
      },
    ],
    shipping_fee: 20,
  },
];

const showCategories = (categories) => {
  let showNames = [];
  categories.map((item, index) => {
    showNames.push(item.name);
  });
  return showNames.join(", ");
};

const ShippingTable = () => {
  const [openModal, setOpenModal] = useState(false);
  const [selectedRow, setSelectedRow] = useState();  
  const onCloseModal = () => {
    setOpenModal(false);
  }
  const handleOpenModal = (row) => {
    setSelectedRow(row);
    setOpenModal(true);
  }
  
  return (
    <div>
      shipping table
      {/* <Table striped bordered hover>
        <thead>
          <tr>
            <th>#</th>
            <th>The categories included in the order.</th>
            <th>Shipping Fee</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          {exampleData.length > 0 &&
            exampleData.map((item, index) => (
              <tr key={index}>
                <td>{item.id}</td>
                <td>{showCategories(item.category_includes)}</td>
                <td>{item.shipping_fee}</td>
                <td>
                  <div className="d-flex gap-3">
                    <button type="button" className="btn btn-secondary edit-btn" onClick={()=>handleOpenModal(item)}>
                      Edit
                    </button>
                    <button type="button" className="btn btn-danger edit-btn">
                      Remove
                    </button>
                  </div>
                </td>
              </tr>
            ))}
        </tbody>

      </Table> */}
    </div>
  );
};

export default ShippingTable;
