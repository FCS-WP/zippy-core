import { Box, Typography } from "@mui/material";
import React from "react";

const SummaryCard = ({ title, value }) => {
  return (
    <Box
      sx={{
        flex: 1,
        p: 2,
        m: 1,
        bgcolor: "#fff",
        borderRadius: 2,
        boxShadow: "0 2px 8px rgba(0,0,0,0.1)",
        minWidth: 150,
      }}
    >
      <Typography variant="subtitle2" color="textSecondary">
        {title}
      </Typography>
      <Typography variant="h5" sx={{ mt: 1 }}>
        {value}
      </Typography>
    </Box>
  );
};

export default SummaryCard;
