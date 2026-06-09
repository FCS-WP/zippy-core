import React, { useState } from "react";
import { Box, Button, Menu, MenuItem, Divider } from "@mui/material";
import DownloadIcon from "@mui/icons-material/Download";
import { Api } from "../../../api/admin";
import { useOrderProvider } from "../../../context/OrderContext";
import { downloadBase64File } from "../../../utils/FileHelper";
import { toast } from "react-toastify";
import BatchExportModal from "./modals/BatchExportModal";

const ExportButton = () => {
  const { filteredOrders, rowsPerPage } = useOrderProvider();

  const [anchorEl, setAnchorEl] = useState(null);
  const [openBatchModal, setOpenBatchModal] = useState(false);
  const [batchFormat, setBatchFormat] = useState('csv');
  const open = Boolean(anchorEl);

  const handleClick = (event) => {
    setAnchorEl(event.currentTarget);
  };

  const handleClose = () => {
    setAnchorEl(null);
  };

  const handleExport = async (type) => {
    handleClose();

    let filter = filteredOrders || {};

    try {
      const res = await Api.exportOrders({
        format: type,
        filter: filter,
        limit: rowsPerPage,
      });

      if (res.data?.status === "success") {
        const { file_base64, file_name, file_type } = res.data;
        if (!file_base64) {
          toast.warning("The file is empty. Nothing to download.");
          return;
        }

        downloadBase64File(file_base64, file_name, file_type);
        toast.success("File downloaded successfully!");
      } else {
        const errorMessage = res.error?.message || res.data?.message || "Failed to export orders.";
        toast.error(errorMessage);
      }
    } catch (error) {
      toast.error("An error occurred during export.");
    }
  };

  const handleOpenBatchExport = (format) => {
    handleClose();
    setBatchFormat(format);
    setOpenBatchModal(true);
  };

  return (
    <>
      <Button
        variant="contained"
        color="primary"
        startIcon={<DownloadIcon />}
        onClick={handleClick}
        sx={{ mr: 1 }}
      >
        Export
      </Button>

      <Menu
        anchorEl={anchorEl}
        open={open}
        onClose={handleClose}
        anchorOrigin={{ vertical: "bottom", horizontal: "left" }}
        transformOrigin={{ vertical: "top", horizontal: "left" }}
      >
        <MenuItem onClick={() => handleExport("pdf")}>
          Export as PDF (Current Page)
        </MenuItem>
        <MenuItem onClick={() => handleExport("csv")}>
          Export as CSV (Current Page)
        </MenuItem>
        <Divider />
        <MenuItem onClick={() => handleOpenBatchExport("pdf")}>
          <DownloadIcon sx={{ mr: 1, fontSize: 18 }} /> Export All Orders (PDF)
        </MenuItem>
        <MenuItem onClick={() => handleOpenBatchExport("csv")}>
          <DownloadIcon sx={{ mr: 1, fontSize: 18 }} /> Export All Orders (CSV)
        </MenuItem>
      </Menu>

      {/* New Batch Export Modal */}
      <BatchExportModal
        open={openBatchModal}
        format={batchFormat}
        filters={filteredOrders || {}}
        onClose={() => setOpenBatchModal(false)}
      />
    </>
  );
};

export default ExportButton;
