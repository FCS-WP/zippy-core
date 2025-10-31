import React, { useState, useEffect, useCallback, use } from "react";
import {
  Box,
  CircularProgress,
  Typography,
  Button,
  TextField,
  FormControl,
  Select,
  MenuItem,
} from "@mui/material";
import { toast, ToastContainer } from "react-toastify";

const Orders = ({}) => {
  return (
    <Box p={3}>
      <Typography variant="h4" gutterBottom>
        Orders Page
      </Typography>
      <ToastContainer />
    </Box>
  );
};

export default Orders;
