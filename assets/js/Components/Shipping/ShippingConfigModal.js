import {
  Box,
  Button,
  Dialog,
  DialogActions,
  DialogContent,
  DialogContentText,
  DialogTitle,
  FormControl,
  Grid,
  InputAdornment,
  InputLabel,
  OutlinedInput,
  Typography,
} from "@mui/material";
import React, { useEffect, useState } from "react";
import EditNoteIcon from "@mui/icons-material/EditNote";
import SearchBox from "../Search/SearchBox";

const ShippingConfigModal = ({ data, show, onClose }) => {
  const [costValue, setCostValue] = useState(null);
  const handleChangeCost = (e) => {
    if (e.target.value < 0) {
      return;
    }
    setCostValue(e.target.value);
  };

  const handleSaveData = (e) => {
    e.preventDefault();
    console.log("Submit form, data:", data);
    onClose();
  };

  useEffect(() => {
    return () => {};
  }, []);

  return (
    <>
      <Dialog open={show} onClose={onClose} fullWidth maxWidth="md">
        <DialogTitle textAlign={"center"} fontWeight={700} fontSize={24}>
          {"Update Shipping Fee"}
        </DialogTitle>
        <DialogContent>
          <Grid container m={2} rowSpacing={3}>
            <Grid
              size={12}
              display={"flex"}
              justifyContent={"center"}
              alignItems={"center"}
              gap={3}
            >
              <Grid size={3}>
                <Typography variant="body2" fontSize={16} fontWeight={600}>
                  Include categories:
                </Typography>
              </Grid>
              <Grid size={6}>
                <SearchBox />
              </Grid>
            </Grid>
            <Grid
              size={12}
              display={"flex"}
              justifyContent={"center"}
              alignItems={"center"}
              gap={3}
            >
              <Grid size={3}>
                <Typography variant="body2" fontSize={16} fontWeight={600}>
                  Shipping Fee:{" "}
                </Typography>
              </Grid>
              <Grid size={6}>
                <FormControl fullWidth>
                  <InputLabel htmlFor="outlined-adornment-amount">
                    Amount
                  </InputLabel>
                  <OutlinedInput
                    size="small"
                    type="number"
                    value={costValue ?? ""}
                    onChange={handleChangeCost}
                    id="outlined-adornment-amount"
                    startAdornment={
                      <InputAdornment position="start">$</InputAdornment>
                    }
                    label="Amount"
                  />
                </FormControl>
              </Grid>
            </Grid>
            <Grid
              size={12}
              display={"flex"}
              justifyContent={"center"}
              alignItems={"center"}
              gap={3}
            >
              <Grid size={3}>
                <Typography variant="body2" fontSize={16} fontWeight={600}>
                  Note:
                </Typography>
              </Grid>
              <Grid size={6}>
                <FormControl fullWidth>
                  <InputLabel htmlFor="input-note">Note</InputLabel>
                  <OutlinedInput
                    size="small"
                    type="text"
                    id="input-note"
                    startAdornment={
                      <InputAdornment position="start">
                        <EditNoteIcon />
                      </InputAdornment>
                    }
                    label="Note"
                  />
                </FormControl>
              </Grid>
            </Grid>
          </Grid>
        </DialogContent>
        <DialogActions>
          <Button onClick={onClose}>Cancel</Button>
          <Button variant="contained" onClick={handleSaveData} autoFocus>
            Update
          </Button>
        </DialogActions>
      </Dialog>
    </>
  );
};

export default ShippingConfigModal;
