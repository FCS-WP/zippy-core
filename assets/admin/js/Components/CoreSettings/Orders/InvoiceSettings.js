import React, { useState } from "react";
import {
  Box,
  Button,
  Grid,
  IconButton,
  TextField,
  Typography,
  Paper,
} from "@mui/material";
import { Add, Delete } from "@mui/icons-material";
import {
  convertNameToSlug,
  convertSlugToName,
} from "../../../helper/table-helper";
import { showAlert } from "../../../helper/alert-helper";
import { SettingApi } from "../../../api/admin";

const InvoiceSettings = ({ data }) => {
  const [rows, setRows] = useState(data);
  const [isLoading, setIsLoading] = useState(false);

  const handleAddRow = () => {
    setRows((prev) => [...prev, { key: "", data: {type: 'text', value: '', position: 'header' } }]);
  };

  const handleChange = (index, field, value) => {
    setRows((prev) => {
      const updated = [...prev];
      if (field == 'key') {
        updated[index]['key'] = value;
      } else {
        updated[index]['data'][field] = value;
      }
      return updated;
    });
  };

  const handleRemoveRow = (index) => {
    setRows((prev) => prev.filter((_, i) => i !== index));
  };

  const triggerSaveConfigs = async () => {
    const newData = checkAndRefactorData();
    if (newData.message) {
      showAlert("error", "Failed", newData.message);
      return;
    }
    const params = {
      new_invoices_options: newData,
    };
    const { data: response } = await SettingApi.updateInvoiceOptions(params);
    console.log("response update", response);
    if (response && response.status == "success") {
      showAlert("success", "Successfully", "New changes have been updated!");
      setTimeout(() => {
        window.location.reload();
      }, 3000);
    } else {
      showAlert(
        "error",
        "Failed",
        "Failed to save changes. Please reload and try again!"
      );
    }

    setIsLoading(false);
    return;

  };

  const checkAndRefactorData = () => {
    let existed_keys = [];
    let error = null;
    const refactoredData = rows.map((row) => {
      const new_key = convertNameToSlug(row.key);
      if (existed_keys.includes(new_key)) {
        let message = `Field name ${row.key} has been duplicated!`;
        error = {
          message,
        };
      }

      existed_keys.push(new_key);

      return { ...row, key: new_key };
    });
    if (error) {
      return error;
    }
    return refactoredData;
  };

  return (
    <Grid container spacing={3}>
      <Grid size={12}>
        {rows.map((row, index) => (
          <Box
            key={index}
            elevation={1}
            sx={{
              width: "100%",
              alignItems: "center",
              mb: 2,
              gap: 2,
            }}
          >
            <Grid container spacing={2}>
              <Grid size={3}>
                <TextField
                  size="small"
                  label="Field Name"
                  variant="outlined"
                  required
                  fullWidth
                  disabled={row?.key === "invoice-logo" ? true : false}
                  value={convertSlugToName(row?.key)}
                  onChange={(e) => handleChange(index, "key", e.target.value)}
                />
              </Grid>
              <Grid size={4}>
                <TextField
                  size="small"
                  label="Field Value"
                  variant="outlined"
                  required
                  fullWidth
                  value={row?.data.value ?? ""}
                  onChange={(e) => handleChange(index, "value", e.target.value)}
                />
              </Grid>
              <Grid size={2}>
                <TextField
                  size="small"
                  label="Field Type"
                  disabled={row?.key === "invoice-logo" ? true : false}
                  variant="outlined"
                  required
                  fullWidth
                  value={row?.data.type ?? ""}
                  onChange={(e) => handleChange(index, "type", e.target.value)}
                />
              </Grid>
              <Grid size={2}>
                <TextField
                  size="small"
                  label="Field Position"
                  variant="outlined"
                  disabled={row?.key === "invoice-logo" ? true : false}
                  fullWidth
                  required
                  value={row?.data.position ?? ""}
                  onChange={(e) =>
                    handleChange(index, "position", e.target.value)
                  }
                />
              </Grid>
              <Grid size={1} textAlign="center">
                {rows.length > 1 && (
                  <IconButton
                    color="error"
                    onClick={() => handleRemoveRow(index)}
                    aria-label="delete"
                  >
                    <Delete />
                  </IconButton>
                )}
              </Grid>
            </Grid>
          </Box>
        ))}

        <Button
          variant="contained"
          startIcon={<Add />}
          onClick={handleAddRow}
          sx={{ mt: 2 }}
        >
          Add Row
        </Button>
      </Grid>
      {/* Preview */}
      <Grid size={12}>
        <Typography variant="subtitle1" sx={{ fontWeight: 500, mb: 1 }}>
          Current Values:
        </Typography>
        <Paper
          sx={{
            p: 2,
            backgroundColor: "#f9f9f9",
            fontFamily: "monospace",
            fontSize: "0.9rem",
          }}
        >
          {JSON.stringify(rows, null, 2)}
        </Paper>
      </Grid>
      <Button
        onClick={triggerSaveConfigs}
        disabled={rows.length === 0 ? true : false}
        sx={{ mt: 2, textTransform: "capitalize" }}
        variant="contained"
        color="primary"
        loading={isLoading}
      >
        Save Changes
      </Button>
    </Grid>
  );
};

export default InvoiceSettings;
