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

const ShippingConfigModal = ({ data, show, onClose }) => {
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
      <Dialog open={show} onClose={onClose} fullWidth maxWidth='md'>
        <DialogTitle>{"Update Shipping Fee"}</DialogTitle>
        <DialogContent>
          <Grid container m={2}>
            <Grid size={12} display={'flex'} alignItems={'center'} gap={3}>
              <Box>
                <Typography variant="body2" fontSize={16} fontWeight={600}>
                  Shipping Fee:{" "}
                </Typography>
              </Box>
              <Box>
                <FormControl>
                  <InputLabel htmlFor="outlined-adornment-amount">
                    Amount
                  </InputLabel>
                  <OutlinedInput
                    size="small"
                    type="number"
                    id="outlined-adornment-amount"
                    startAdornment={
                      <InputAdornment position="start">$</InputAdornment>
                    }
                    label="Amount"
                  />
                </FormControl>
              </Box>
            </Grid>
            <Grid size={12} display={'flex'} alignItems={'center'} gap={3}>
              <Box>
                <Typography variant="body2" fontSize={16} fontWeight={600}>
                  Shipping Fee:{" "}
                </Typography>
              </Box>
              <Box>
                <FormControl>
                  <InputLabel htmlFor="outlined-adornment-amount">
                    Amount
                  </InputLabel>
                  <OutlinedInput
                    size="small"
                    type="number"
                    id="outlined-adornment-amount"
                    startAdornment={
                      <InputAdornment position="start">$</InputAdornment>
                    }
                    label="Amount"
                  />
                </FormControl>
              </Box>
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
