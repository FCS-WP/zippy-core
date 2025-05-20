import React, { useState } from "react";
import ShippingConfigModal from "./ShippingConfigModal";
import EditIcon from "@mui/icons-material/Edit";
import DeleteIcon from "@mui/icons-material/Delete";
import DataTable from "./DataTable";
import { GridActionsCellItem } from "@mui/x-data-grid";
import { useShippingProvider } from "../../contexts/ShippingProvider";

const ShippingTable = () => {
  const { shippingData } = useShippingProvider();
  const [diaglogData, setDiaglogData] = useState(null);
  const [showModal, setShowModal] = useState(false);

  const showCategories = (categories) => {
    let showNames = [];
    categories.map((item, index) => {
      showNames.push(item.name);
    });
    return showNames.join(", ");
  };

  // Table Function

  const tableData = shippingData
    ? shippingData.map((item) => {
        return {
          id: item.id,
          categories: showCategories(item.category_includes),
          fee: item.shipping_fee,
          note: item.note ?? "",
        };
      })
    : [];

  const columns = [
    { field: "id", headerName: "ID", width: 70 },
    { field: "categories", headerName: "Categories", width: 300 },
    { field: "fee", headerName: "Fee", width: 100 },
    { field: "note", headerName: "Note", width: 100 },
    {
      field: "actions",
      type: "actions",
      headerName: "Actions",
      width: 100,
      getActions: (params) => [
        <GridActionsCellItem
          icon={<EditIcon />}
          label="Edit"
          onClick={() => handleUpdateRow(params.row)}
        />,
        <GridActionsCellItem
          icon={<DeleteIcon />}
          label="Delete"
          onClick={() => handleDeleteRows(params.id)}
        />,
      ],
    },
  ];

  const handleDeleteRows = async (rows = []) => {
    console.log("deleteRows", rows);
  };

  const handleUpdateRow = async (row) => {
    const originData = shippingData.find((item) => item.id === row.id);
    setDiaglogData(originData);
    setShowModal(true);
  };

  // Dialog

  const onCloseModal = () => {
    setShowModal(false);
  };

  return (
    <div>
      <DataTable initRows={tableData} columns={columns} />
      <ShippingConfigModal
        data={diaglogData}
        show={showModal}
        onClose={onCloseModal}
      />
    </div>
  );
};

export default ShippingTable;
