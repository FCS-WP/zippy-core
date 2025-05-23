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
import BorderColorIcon from '@mui/icons-material/BorderColor';

const ShippingConfigModal = ({ data, show, onClose }) => {
  const [shippingFee, setShippingFee] = useState(null);
  const [name, setName] = useState('');
  const [note, setNote] = useState('');
  const [includeCategories, setIncludeCategories] = useState([]);

  const handleChangeShippingFee = (e) => {
    if (e.target.value < 0) {
      return;
    }
    setShippingFee(e.target.value);
  };

  const handleSaveData = (e) => {
    e.preventDefault();
    onClose();
  };

  useEffect(() => {
    if (data) {
      setShippingFee(data.shipping_fee);
      setName(data.name);
      setNote(data.note);
      setIncludeCategories(data.category_includes);
    }
  }, [data]);

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
                <SearchBox includeCategories={includeCategories}/>
              </Grid>
            </Grid>
            {/* Name */}
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
                  <InputLabel htmlFor="input-name">Name</InputLabel>
                  <OutlinedInput
                    size="small"
                    type="text"
                    id="input-name"
                    value={name ?? ""}
                    onChange={(e)=>setName(e.target.value)}
                    startAdornment={
                      <InputAdornment position="start">
                        <BorderColorIcon />
                      </InputAdornment>
                    }
                    label="Name"
                  />
                </FormControl>
              </Grid>
            </Grid>
            {/* Shipping Fee */}
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
                    value={shippingFee ?? ""}
                    onChange={handleChangeShippingFee}
                    id="outlined-adornment-amount"
                    startAdornment={
                      <InputAdornment position="start">$</InputAdornment>
                    }
                    label="Amount"
                  />
                </FormControl>
              </Grid>
            </Grid>
            {/* Note */}
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
                    value={note ?? ""}
                    onChange={(e)=>setNote(e.target.value)}
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
        <DialogActions sx={{ p: 3 }}>
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
